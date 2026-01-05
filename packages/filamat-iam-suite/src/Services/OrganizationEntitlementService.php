<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services;

use Filamat\IamSuite\Models\Organization;
use Filamat\IamSuite\Models\Tenant;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class OrganizationEntitlementService
{
    public function __construct(
        protected ModuleCatalog $moduleCatalog,
        protected AuditService $auditService,
        protected RoleTemplateService $roleTemplateService
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function getEntitlements(Organization $organization): array
    {
        $settings = (array) ($organization->settings ?? []);
        $entitlements = (array) (data_get($settings, 'entitlements', []) ?? []);

        return $this->normalizeEntitlements($entitlements);
    }

    /**
     * @param  array<string, mixed>  $entitlements
     */
    public function updateEntitlements(Organization $organization, array $entitlements, ?Authenticatable $actor = null): Organization
    {
        $normalized = $this->normalizeEntitlements($entitlements);

        $settings = (array) ($organization->settings ?? []);
        $settings['entitlements'] = $normalized;
        $organization->settings = $settings;
        $organization->save();

        $this->auditService->log('organization.entitlements.updated', $organization, [
            'entitlements' => $normalized,
        ], $actor, null);

        return $organization;
    }

    /**
     * @return array<int, string>
     */
    public function allowedModules(Organization $organization): array
    {
        $entitlements = $this->getEntitlements($organization);
        $modules = Arr::wrap($entitlements['modules'] ?? []);
        $modulesExplicit = (bool) ($entitlements['modules_explicit'] ?? false);

        if ($modules === [] && ! $modulesExplicit) {
            $modules = array_keys($this->moduleCatalog->modules());
        }

        $modules[] = 'filamat-iam-suite';

        return array_values(array_unique(array_filter($modules)));
    }

    /**
     * @return array<int, string>
     */
    public function allowedModulesForTenant(Tenant $tenant): array
    {
        $organization = $tenant->organization;
        if (! $organization) {
            return [];
        }

        $orgModules = $this->allowedModules($organization);
        $tenantModules = Arr::wrap(data_get($tenant->settings, 'access.modules', []));
        $tenantModules = array_values(array_filter($tenantModules));

        if ($tenantModules === []) {
            return $orgModules;
        }

        return array_values(array_intersect($orgModules, $tenantModules));
    }

    public function allowsPermission(Tenant $tenant, string $permissionKey, array &$trace): bool
    {
        $organization = $tenant->organization;
        if (! $organization) {
            return true;
        }

        $entitlements = $this->getEntitlements($organization);
        $status = $this->resolveStatus($entitlements);

        if (! in_array($status['state'], ['active', 'trial'], true)) {
            $exempt = (array) config('filamat-iam.organization_entitlements.exempt_permissions', []);
            if (! in_array($permissionKey, $exempt, true)) {
                $trace[] = [
                    'source' => 'organization_entitlement',
                    'effect' => 'deny',
                    'detail' => $status['reason'],
                ];

                return false;
            }
        }

        $allowedModules = $this->allowedModulesForTenant($tenant);
        if ($allowedModules !== []) {
            $module = $this->moduleCatalog->moduleForPermission($permissionKey);
            if (! $module || ! in_array($module, $allowedModules, true)) {
                $trace[] = [
                    'source' => 'organization_entitlement',
                    'effect' => 'deny',
                    'detail' => 'ماژول این مجوز در پلن سازمان فعال نیست.',
                ];

                return false;
            }
        }

        $permissionAllowList = Arr::wrap($entitlements['permissions'] ?? []);
        if ($permissionAllowList !== [] && ! in_array($permissionKey, $permissionAllowList, true)) {
            $trace[] = [
                'source' => 'organization_entitlement',
                'effect' => 'deny',
                'detail' => 'مجوز خارج از لیست دسترسی سازمان است.',
            ];

            return false;
        }

        $trace[] = [
            'source' => 'organization_entitlement',
            'effect' => 'allow',
            'detail' => $status['state'] === 'trial' ? 'سازمان در دوره آزمایشی فعال است.' : 'پلن سازمان فعال است.',
        ];

        return true;
    }

    public function canCreateTenant(Organization $organization): bool
    {
        $entitlements = $this->getEntitlements($organization);
        $status = $this->resolveStatus($entitlements);
        if (! in_array($status['state'], ['active', 'trial'], true)) {
            return false;
        }

        $maxTenants = $entitlements['max_tenants'] ?? null;
        if (! $maxTenants) {
            return true;
        }

        $current = $organization->tenants()->count();

        return $current < (int) $maxTenants;
    }

    public function canInviteUser(Tenant $tenant): bool
    {
        $organization = $tenant->organization;
        if (! $organization) {
            return true;
        }

        $entitlements = $this->getEntitlements($organization);
        $status = $this->resolveStatus($entitlements);
        if (! in_array($status['state'], ['active', 'trial'], true)) {
            return false;
        }

        $maxUsers = $entitlements['max_users'] ?? null;
        if (! $maxUsers) {
            return true;
        }

        $current = $tenant->users()->count();

        return $current < (int) $maxUsers;
    }

    public function syncOrganizationAccess(Organization $organization, ?Authenticatable $actor = null): void
    {
        foreach ($organization->tenants()->get() as $tenant) {
            $modules = $this->allowedModulesForTenant($tenant);
            $permissions = $this->moduleCatalog->permissionsForModules($modules);
            $settings = (array) ($tenant->settings ?? []);
            data_set($settings, 'access.modules', $modules);
            $tenant->settings = $settings;
            $tenant->save();
            $this->roleTemplateService->syncTemplatesForTenant($tenant, $permissions);
        }

        $this->auditService->log('organization.entitlements.synced', $organization, [], $actor, null);
    }

    /**
     * @param  array<string, mixed>  $entitlements
     * @return array{state: string, reason: string}
     */
    protected function resolveStatus(array $entitlements): array
    {
        $status = (string) ($entitlements['status'] ?? 'active');
        $now = now();

        $startsAt = $this->parseDate($entitlements['starts_at'] ?? null);
        if ($startsAt && $now->lt($startsAt)) {
            return ['state' => 'pending', 'reason' => 'پلن هنوز فعال نشده است.'];
        }

        $endsAt = $this->parseDate($entitlements['ends_at'] ?? null);
        if ($endsAt && $now->gt($endsAt)) {
            return ['state' => 'expired', 'reason' => 'پلن سازمان منقضی شده است.'];
        }

        if ($status === 'trial') {
            $trialEnds = $this->parseDate($entitlements['trial_ends_at'] ?? null);
            if ($trialEnds && $now->gt($trialEnds)) {
                return ['state' => 'expired', 'reason' => 'دوره آزمایشی سازمان پایان یافته است.'];
            }
        }

        if ($status === 'inactive') {
            return ['state' => 'inactive', 'reason' => 'پلن سازمان غیرفعال است.'];
        }

        return ['state' => $status === 'trial' ? 'trial' : 'active', 'reason' => 'active'];
    }

    /**
     * @param  array<string, mixed>  $entitlements
     * @return array<string, mixed>
     */
    protected function normalizeEntitlements(array $entitlements): array
    {
        $normalized = $entitlements;

        $normalized['modules'] = array_values(array_unique(array_filter(Arr::wrap($entitlements['modules'] ?? []))));
        $normalized['permissions'] = array_values(array_unique(array_filter(Arr::wrap($entitlements['permissions'] ?? []))));
        $normalized['feature_flags'] = (array) ($entitlements['feature_flags'] ?? []);
        $normalized['quotas'] = (array) ($entitlements['quotas'] ?? []);
        $normalized['modules_explicit'] = (bool) ($entitlements['modules_explicit'] ?? false);
        $normalized['plan'] = $entitlements['plan'] ?? null;
        $normalized['status'] = $entitlements['status'] ?? 'active';
        $normalized['max_tenants'] = $this->toNullableInt($entitlements['max_tenants'] ?? null);
        $normalized['max_users'] = $this->toNullableInt($entitlements['max_users'] ?? null);
        $normalized['starts_at'] = $this->normalizeDate($entitlements['starts_at'] ?? null);
        $normalized['ends_at'] = $this->normalizeDate($entitlements['ends_at'] ?? null);
        $normalized['trial_ends_at'] = $this->normalizeDate($entitlements['trial_ends_at'] ?? null);
        $normalized['notes'] = $entitlements['notes'] ?? null;

        return $normalized;
    }

    protected function parseDate(mixed $value): ?Carbon
    {
        if ($value instanceof Carbon) {
            return $value;
        }

        if (! is_string($value) || $value === '') {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    protected function normalizeDate(mixed $value): ?string
    {
        $parsed = $this->parseDate($value);

        return $parsed?->toISOString();
    }

    protected function toNullableInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = (int) $value;

        return $value > 0 ? $value : null;
    }
}
