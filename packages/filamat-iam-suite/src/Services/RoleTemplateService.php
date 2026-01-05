<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\CorePermissions;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleTemplateService
{
    /**
     * @param  array<int, string>  $allowedPermissions
     * @return array<int, Role>
     */
    public function syncTemplatesForTenant(Tenant $tenant, array $allowedPermissions = []): array
    {
        app(PermissionRegistrar::class)->setPermissionsTeamId($tenant->getKey());

        $roles = [];
        $allowedPermissions = array_values(array_unique(array_filter($allowedPermissions)));
        $allowedMap = $allowedPermissions !== [] ? array_fill_keys($allowedPermissions, true) : [];

        foreach (CorePermissions::roleTemplates() as $roleName => $templatePermissions) {
            $permissions = $this->resolveTemplatePermissions($roleName, $templatePermissions, $allowedPermissions, $allowedMap);

            $permissionModels = collect($permissions)->map(function (string $permissionKey) use ($tenant) {
                return Permission::query()->firstOrCreate([
                    'name' => $permissionKey,
                    'guard_name' => 'web',
                    'tenant_id' => $tenant->getKey(),
                ]);
            });

            $role = Role::query()->firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
                'tenant_id' => $tenant->getKey(),
            ]);

            $role->syncPermissions($permissionModels);
            $roles[] = $role;
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $roles;
    }

    public function assignRoleForPivot(Tenant $tenant, Authenticatable $user, string $pivotRole): void
    {
        $roleName = $this->roleNameForPivot($pivotRole);
        if (! $roleName || ! method_exists($user, 'assignRole')) {
            return;
        }

        app(PermissionRegistrar::class)->setPermissionsTeamId($tenant->getKey());

        $role = Role::query()
            ->where('tenant_id', $tenant->getKey())
            ->where('name', $roleName)
            ->first();

        if (! $role) {
            return;
        }

        $user->assignRole($role);
    }

    public function roleNameForPivot(string $pivotRole): ?string
    {
        return match ($pivotRole) {
            'owner' => 'tenant_owner',
            'admin' => 'tenant_admin',
            'member' => 'tenant_member',
            default => null,
        };
    }

    /**
     * @param  array<int, string>  $templatePermissions
     * @param  array<int, string>  $allowedPermissions
     * @param  array<string, bool>  $allowedMap
     * @return array<int, string>
     */
    protected function resolveTemplatePermissions(
        string $roleName,
        array $templatePermissions,
        array $allowedPermissions,
        array $allowedMap
    ): array {
        $permissions = array_values(array_unique(array_filter(Arr::wrap($templatePermissions))));

        $includeModules = (bool) (config('filamat-iam.role_templates.include_module_permissions.'.$roleName) ?? false);
        if ($includeModules && $allowedPermissions !== []) {
            $permissions = array_values(array_unique(array_merge($permissions, $allowedPermissions)));
        }

        if ($allowedMap !== []) {
            $permissions = array_values(array_filter($permissions, fn (string $permission) => isset($allowedMap[$permission])));
        }

        sort($permissions);

        return $permissions;
    }
}
