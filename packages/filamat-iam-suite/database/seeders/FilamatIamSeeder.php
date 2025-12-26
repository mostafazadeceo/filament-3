<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Database\Seeders;

use Filamat\IamSuite\Models\PermissionTemplate;
use Filamat\IamSuite\Models\SubscriptionPlan;
use Filamat\IamSuite\Support\CorePermissions;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class FilamatIamSeeder extends Seeder
{
    public function run(): void
    {
        $guard = 'web';

        $permissions = CorePermissions::all();

        foreach ($permissions as $permission) {
            Permission::query()->firstOrCreate([
                'name' => $permission,
                'guard_name' => $guard,
                'tenant_id' => null,
            ]);
        }

        $roles = CorePermissions::roleTemplates();

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::query()->firstOrCreate([
                'name' => $roleName,
                'guard_name' => $guard,
                'tenant_id' => null,
            ]);

            $role->syncPermissions($rolePermissions);

            PermissionTemplate::query()->firstOrCreate([
                'name' => $roleName,
                'tenant_id' => null,
            ], [
                'type' => 'role',
                'permissions' => $rolePermissions,
            ]);
        }

        foreach (CorePermissions::planTemplates() as $code => $template) {
            SubscriptionPlan::query()->firstOrCreate([
                'code' => $code,
            ], $template);
        }
    }
}
