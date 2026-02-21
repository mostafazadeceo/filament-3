<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services;

use Filamat\IamSuite\Models\Organization;
use Filamat\IamSuite\Models\Tenant;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class OrganizationProvisioningService
{
    public function __construct(
        protected OrganizationEntitlementService $entitlementService,
        protected TenantProvisioningService $tenantProvisioningService
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function createOrganizationWithTenant(array $data, Authenticatable $actor): Organization
    {
        return DB::transaction(function () use ($data, $actor): Organization {
            $organization = Organization::query()->create([
                'name' => $data['organization_name'] ?? 'Organization',
                'owner_user_id' => $data['organization_owner_id'] ?? $actor->getAuthIdentifier(),
                'shared_data_mode' => $data['shared_data_mode'] ?? 'isolated',
                'settings' => [],
            ]);

            $this->entitlementService->updateEntitlements($organization, $data['entitlements'] ?? [], $actor);

            $ownerId = $data['tenant_owner_id'] ?? $data['organization_owner_id'] ?? $actor->getAuthIdentifier();
            $userModel = config('auth.providers.users.model');
            $owner = $userModel::query()->find($ownerId) ?? $actor;

            $tenant = $this->tenantProvisioningService->createTenant(
                $organization,
                $data['tenant'] ?? [],
                $owner,
                $data['modules'] ?? [],
                $actor
            );

            $this->provisionChatForTenant(
                $tenant,
                Arr::wrap($data['modules'] ?? []),
                (array) ($data['entitlements'] ?? [])
            );

            return $organization;
        });
    }

    /**
     * @param  array<int, string>  $modules
     * @param  array<string, mixed>  $entitlements
     */
    protected function provisionChatForTenant(Tenant $tenant, array $modules, array $entitlements): void
    {
        $modules = array_values(array_filter(array_map('strval', $modules)));
        if (! in_array('chat', $modules, true)) {
            return;
        }

        if (! class_exists(\Haida\FilamentChat\Models\ChatConnection::class)
            || ! class_exists(\Haida\FilamentChat\Services\ChatConnectionService::class)) {
            return;
        }

        try {
            $connectionModel = \Haida\FilamentChat\Models\ChatConnection::class;
            $baseUrl = (string) config('filamat-iam.chat.shared_base_url', 'https://chat.abrak.org');
            $connectionName = (string) config('filamat-iam.chat.shared_connection_name', 'shared-rocket-chat');
            $teamPrefix = (string) config('filamat-iam.chat.default_team_prefix', 'tenant-');
            $roomPrefix = (string) config('filamat-iam.chat.default_room_prefix', 'room-');
            $ownerManageEnabled = $this->resolveTenantOwnerManageFlag($entitlements);
            [$sharedUserId, $sharedToken] = $this->resolveSharedChatCredentials();

            /** @var \Haida\FilamentChat\Models\ChatConnection $connection */
            $connection = $connectionModel::query()->firstOrNew([
                'tenant_id' => $tenant->getKey(),
                'name' => $connectionName,
            ]);

            $connection->provider = $connection->provider ?: 'rocket_chat';
            $connection->base_url = $connection->base_url ?: $baseUrl;
            $connection->status = $connection->status ?: 'active';
            $connection->api_user_id = $connection->api_user_id ?: $sharedUserId;
            $connection->api_token = $connection->api_token ?: $sharedToken;

            $settings = (array) ($connection->settings ?? []);
            $settings['team_prefix'] = $settings['team_prefix'] ?? $teamPrefix;
            $settings['room_prefix'] = $settings['room_prefix'] ?? $roomPrefix;
            $settings['allow_owner_manage'] = $ownerManageEnabled;
            // Keep the shared API service account inside tenant teams so sync/admin APIs stay reliable.
            $settings['remove_service_account_from_team'] = (bool) ($settings['remove_service_account_from_team'] ?? false);
            $settings['quotas'] = [
                'plan' => $this->normalizeQuotaValues((array) data_get($entitlements, 'quotas.chat.plan', [])),
                'trial' => $this->normalizeQuotaValues((array) data_get($entitlements, 'quotas.chat.trial', [])),
            ];

            $roleMap = (array) ($settings['role_map'] ?? []);

            // Tenant owners are elevated at team scope, not as global Rocket.Chat owners.
            // This keeps organizations isolated on a shared chat workspace.
            $ownerRoles = Arr::wrap($roleMap['tenant_owner'] ?? ['user']);
            $ownerRoles = array_values(array_unique(array_filter(array_map('strval', $ownerRoles))));
            $ownerRoles = array_values(array_diff($ownerRoles, ['owner', 'moderator']));
            if (! in_array('user', $ownerRoles, true)) {
                $ownerRoles[] = 'user';
            }

            $roleMap['tenant_owner'] = $ownerRoles;
            $roleMap['tenant_admin'] = Arr::wrap($roleMap['tenant_admin'] ?? ['user']);
            $roleMap['tenant_member'] = Arr::wrap($roleMap['tenant_member'] ?? ['user']);
            $settings['role_map'] = $roleMap;

            $connection->settings = $settings;
            $connection->save();

            if (! $connection->api_user_id || ! $connection->api_token) {
                return;
            }

            /** @var \Haida\FilamentChat\Services\ChatConnectionService $chatService */
            $chatService = app(\Haida\FilamentChat\Services\ChatConnectionService::class);
            $chatService->syncUsers($connection, $tenant);
        } catch (\Throwable $exception) {
            report($exception);
        }
    }

    /**
     * @return array{0: string, 1: string}
     */
    protected function resolveSharedChatCredentials(): array
    {
        $configuredUserId = (string) config('filamat-iam.chat.shared_admin_user_id', '');
        $configuredToken = (string) config('filamat-iam.chat.shared_admin_token', '');

        if ($configuredUserId !== '' && $configuredToken !== '') {
            return [$configuredUserId, $configuredToken];
        }

        if (! class_exists(\Haida\FilamentChat\Models\ChatConnection::class)) {
            return ['', ''];
        }

        /** @var \Haida\FilamentChat\Models\ChatConnection|null $template */
        $template = \Haida\FilamentChat\Models\ChatConnection::query()
            ->where('provider', 'rocket_chat')
            ->whereNotNull('api_user_id')
            ->whereNotNull('api_token')
            ->orderBy('id')
            ->first();

        if (! $template) {
            return ['', ''];
        }

        return [
            (string) ($template->api_user_id ?? ''),
            (string) ($template->api_token ?? ''),
        ];
    }

    /**
     * @param  array<string, mixed>  $entitlements
     */
    protected function resolveTenantOwnerManageFlag(array $entitlements): bool
    {
        $featureFlag = (string) config('filamat-iam.chat.owner_manage_flag', 'tenant_owner_manage');
        $flags = Arr::wrap(data_get($entitlements, 'feature_flags.chat', []));
        $flags = array_values(array_filter(array_map('strval', $flags)));

        if ($flags === []) {
            return true;
        }

        return in_array($featureFlag, $flags, true);
    }

    /**
     * @param  array<string, mixed>  $values
     * @return array<string, int>
     */
    protected function normalizeQuotaValues(array $values): array
    {
        $normalized = [];

        foreach ($values as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            if (! is_numeric($value)) {
                continue;
            }

            $normalized[(string) $key] = (int) $value;
        }

        return $normalized;
    }
}
