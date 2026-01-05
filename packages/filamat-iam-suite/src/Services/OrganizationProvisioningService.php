<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services;

use Filamat\IamSuite\Models\Organization;
use Illuminate\Contracts\Auth\Authenticatable;
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

            $this->tenantProvisioningService->createTenant(
                $organization,
                $data['tenant'] ?? [],
                $owner,
                $data['modules'] ?? [],
                $actor
            );

            return $organization;
        });
    }
}
