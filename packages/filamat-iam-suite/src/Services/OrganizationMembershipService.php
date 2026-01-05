<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services;

use Filamat\IamSuite\Models\Organization;
use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\OrganizationAccess;
use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class OrganizationMembershipService
{
    public function __construct(
        protected ModuleCatalog $moduleCatalog,
        protected RoleTemplateService $roleTemplateService
    ) {}

    /**
     * @param  array<int, string>  $roles
     */
    public function assignRoles(
        Authenticatable $user,
        Tenant $tenant,
        array $roles,
        string $pivotRole = 'member'
    ): void {
        if (! method_exists($user, 'assignRole')) {
            return;
        }

        app(PermissionRegistrar::class)->setPermissionsTeamId($tenant->getKey());

        foreach (Arr::wrap($roles) as $roleName) {
            $role = Role::query()
                ->where('tenant_id', $tenant->getKey())
                ->where('name', $roleName)
                ->first();

            if ($role) {
                $user->assignRole($role);
            }
        }

        $this->roleTemplateService->assignRoleForPivot($tenant, $user, $pivotRole);
    }

    /**
     * @param  array<int, string>  $permissions
     */
    public function assignPermissions(Authenticatable $user, Tenant $tenant, array $permissions): void
    {
        foreach (Arr::wrap($permissions) as $permissionKey) {
            Permission::query()->firstOrCreate([
                'name' => $permissionKey,
                'guard_name' => 'web',
                'tenant_id' => $tenant->getKey(),
            ]);

            $user->givePermissionTo($permissionKey);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * @return array<int, string>
     */
    public function availableRoles(Tenant $tenant): array
    {
        $org = $tenant->organization;
        if (! $org) {
            return [];
        }

        $allowedModules = app(OrganizationEntitlementService::class)->allowedModulesForTenant($tenant);
        $allowedPermissions = $this->moduleCatalog->permissionsForModules($allowedModules);

        $roles = Role::query()->where('tenant_id', $tenant->getKey())->pluck('name')->all();
        $roles = array_values(array_unique(array_filter($roles)));
        sort($roles);

        return $roles;
    }

    public function currentOrganization(): ?Organization
    {
        return OrganizationAccess::currentOrganization();
    }

    public function isOrganizationOwner(): bool
    {
        return OrganizationAccess::isCurrentOrganizationOwner();
    }

    public function currentTenant(): ?Tenant
    {
        return TenantContext::getTenant();
    }
}
