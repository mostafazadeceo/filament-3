<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services;

use Filamat\IamSuite\Models\Organization;
use Filamat\IamSuite\Models\Tenant;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class TenantProvisioningService
{
    public function __construct(
        protected ModuleCatalog $moduleCatalog,
        protected RoleTemplateService $roleTemplateService,
        protected OrganizationEntitlementService $entitlementService
    ) {}

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, string>  $modules
     */
    public function createTenant(
        Organization $organization,
        array $data,
        Authenticatable $owner,
        array $modules = [],
        ?Authenticatable $actor = null
    ): Tenant {
        return DB::transaction(function () use ($organization, $data, $owner, $modules, $actor): Tenant {
            $tenant = Tenant::query()->create([
                'name' => $data['name'] ?? 'Tenant',
                'slug' => $data['slug'] ?? null,
                'organization_id' => $organization->getKey(),
                'owner_user_id' => $owner->getAuthIdentifier(),
                'status' => $data['status'] ?? 'active',
                'locale' => $data['locale'] ?? null,
                'timezone' => $data['timezone'] ?? null,
                'settings' => $data['settings'] ?? [],
            ]);

            $this->finalizeTenant($tenant, $owner, $modules, $actor);

            return $tenant;
        });
    }

    /**
     * @param  array<int, string>  $modules
     */
    public function finalizeTenant(
        Tenant $tenant,
        Authenticatable $owner,
        array $modules = [],
        ?Authenticatable $actor = null
    ): Tenant {
        $modules = $this->normalizeModules($tenant, $modules);
        $permissions = $this->moduleCatalog->permissionsForModules($modules);

        $settings = (array) ($tenant->settings ?? []);
        data_set($settings, 'access.modules', $modules);
        $tenant->settings = $settings;
        $tenant->save();

        $this->roleTemplateService->syncTemplatesForTenant($tenant, $permissions);
        $this->attachOwner($tenant, $owner, $actor);
        $this->roleTemplateService->assignRoleForPivot($tenant, $owner, 'owner');

        return $tenant;
    }

    public function syncOwner(Tenant $tenant, ?Authenticatable $actor = null): void
    {
        $ownerId = $tenant->owner_user_id;
        if (! $ownerId) {
            return;
        }

        $userModel = config('auth.providers.users.model');
        $owner = $userModel::query()->find($ownerId);
        if (! $owner) {
            return;
        }

        $this->attachOwner($tenant, $owner, $actor);
        $this->roleTemplateService->assignRoleForPivot($tenant, $owner, 'owner');
    }

    protected function attachOwner(Tenant $tenant, Authenticatable $owner, ?Authenticatable $actor = null): void
    {
        if (! method_exists($owner, 'tenants')) {
            return;
        }

        $owner->tenants()->syncWithoutDetaching([
            $tenant->getKey() => [
                'role' => 'owner',
                'status' => 'active',
                'joined_at' => now(),
                'activated_at' => now(),
                'activated_by_id' => $actor?->getAuthIdentifier() ?? $owner->getAuthIdentifier(),
            ],
        ]);
    }

    /**
     * @param  array<int, string>  $modules
     * @return array<int, string>
     */
    protected function normalizeModules(Tenant $tenant, array $modules): array
    {
        $modules = array_values(array_unique(array_filter($modules)));
        if ($modules === []) {
            $modules = $this->entitlementService->allowedModulesForTenant($tenant);
        }

        if (! in_array('filamat-iam-suite', $modules, true)) {
            $modules[] = 'filamat-iam-suite';
        }

        sort($modules);

        return $modules;
    }
}
