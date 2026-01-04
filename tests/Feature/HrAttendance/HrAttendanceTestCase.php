<?php

namespace Tests\Feature\HrAttendance;

use App\Models\User;
use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

abstract class HrAttendanceTestCase extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'filamat-iam.subscriptions.enforce_access' => false,
        ]);
    }

    protected function tearDown(): void
    {
        TenantContext::setTenant(null);

        parent::tearDown();
    }

    protected function createTenant(string $name = 'Tenant A'): Tenant
    {
        return Tenant::query()->create([
            'name' => $name,
            'slug' => strtolower(str_replace(' ', '-', $name)).'-'.uniqid(),
            'status' => 'active',
        ]);
    }

    /**
     * @param  array<int, string>  $permissions
     */
    protected function createUserWithPermissions(Tenant $tenant, array $permissions): User
    {
        /** @var User $user */
        $user = User::factory()->create();

        app(PermissionRegistrar::class)->setPermissionsTeamId($tenant->getKey());

        $tenant->users()->syncWithoutDetaching([
            $user->getKey() => [
                'role' => 'member',
                'status' => 'active',
                'joined_at' => now(),
            ],
        ]);

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
                'tenant_id' => $tenant->getKey(),
            ]);
        }

        $role = Role::firstOrCreate([
            'name' => 'payroll-admin',
            'guard_name' => 'web',
            'tenant_id' => $tenant->getKey(),
        ]);

        $role->syncPermissions($permissions);
        $user->assignRole($role);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $user;
    }
}
