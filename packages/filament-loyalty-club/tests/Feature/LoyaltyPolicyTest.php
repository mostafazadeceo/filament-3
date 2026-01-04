<?php

namespace Haida\FilamentLoyaltyClub\Tests\Feature;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentLoyaltyClub\Models\LoyaltyCustomer;
use Haida\FilamentLoyaltyClub\Policies\LoyaltyCustomerPolicy;
use Haida\FilamentLoyaltyClub\Tests\Fixtures\User;
use Haida\FilamentLoyaltyClub\Tests\TestCase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class LoyaltyPolicyTest extends TestCase
{
    public function test_policy_denies_without_permission(): void
    {
        $tenant = Tenant::create(['name' => 'Tenant', 'slug' => 'tenant']);
        TenantContext::setTenant($tenant);

        $user = User::query()->create([
            'name' => 'Test',
            'email' => 'denied@example.test',
        ]);

        $this->actingAs($user);

        $customer = LoyaltyCustomer::query()->create([
            'tenant_id' => $tenant->getKey(),
            'first_name' => 'Denied',
        ]);

        $policy = new LoyaltyCustomerPolicy;

        $this->assertFalse($policy->viewAny());
        $this->assertFalse($policy->view($customer));
    }

    public function test_policy_allows_with_permission(): void
    {
        $tenant = Tenant::create(['name' => 'Tenant', 'slug' => 'tenant']);
        TenantContext::setTenant($tenant);

        $user = User::query()->create([
            'name' => 'Allowed',
            'email' => 'allowed@example.test',
        ]);

        $this->actingAs($user);

        $registrar = app(PermissionRegistrar::class);
        $registrar->setPermissionsTeamId($tenant->getKey());
        $registrar->forgetCachedPermissions();

        $permission = Permission::query()->create([
            'name' => 'loyalty.customer.view',
            'guard_name' => 'web',
            'tenant_id' => $tenant->getKey(),
        ]);

        $role = Role::query()->create([
            'name' => 'loyalty_viewer',
            'guard_name' => 'web',
            'tenant_id' => $tenant->getKey(),
        ]);

        $role->givePermissionTo($permission);
        $user->assignRole($role);
        $registrar->setPermissionsTeamId($tenant->getKey());
        $registrar->forgetCachedPermissions();
        $user->refresh();

        $customer = LoyaltyCustomer::query()->create([
            'tenant_id' => $tenant->getKey(),
            'first_name' => 'Allowed',
        ]);

        $policy = new LoyaltyCustomerPolicy;

        $this->assertTrue($policy->viewAny());
        $this->assertTrue($policy->view($customer));
    }
}
