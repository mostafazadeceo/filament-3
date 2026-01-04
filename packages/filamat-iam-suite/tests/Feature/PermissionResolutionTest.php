<?php

declare(strict_types=1);

use Filamat\IamSuite\Models\PermissionOverride;
use Filamat\IamSuite\Models\Subscription;
use Filamat\IamSuite\Models\SubscriptionPlan;
use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Services\AccessService;
use Filamat\IamSuite\Tests\Fixtures\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

it('resolves permission with overrides', function () {
    $user = User::query()->create(['name' => 'User', 'email' => 'u@example.com', 'password' => bcrypt('secret')]);
    $tenant = Tenant::query()->create(['name' => 'Tenant', 'slug' => 'tenant']);

    app(PermissionRegistrar::class)->forgetCachedPermissions();
    app(PermissionRegistrar::class)->setPermissionsTeamId($tenant->getKey());

    $permission = Permission::query()->create([
        'name' => 'orders.view',
        'guard_name' => 'web',
        'tenant_id' => $tenant->getKey(),
    ]);

    $plan = SubscriptionPlan::query()->create([
        'name' => 'Base',
        'code' => 'base-plan',
        'price' => 0,
        'currency' => 'irr',
        'period_days' => 30,
        'features' => [],
    ]);

    Subscription::query()->create([
        'tenant_id' => $tenant->getKey(),
        'user_id' => $user->getKey(),
        'plan_id' => $plan->getKey(),
        'status' => 'active',
    ]);

    $role = Role::query()->create([
        'name' => 'viewer',
        'guard_name' => 'web',
        'tenant_id' => $tenant->getKey(),
    ]);

    $role->givePermissionTo($permission);
    $user->assignRole($role);

    PermissionOverride::query()->create([
        'tenant_id' => $tenant->getKey(),
        'user_id' => $user->getKey(),
        'permission_key' => 'orders.view',
        'effect' => 'deny',
    ]);

    $result = app(AccessService::class)->checkPermission($user, $tenant, 'orders.view');

    expect($result)->toBeFalse();
});

it('denies permissions without active subscription', function () {
    $user = User::query()->create(['name' => 'NoPlan', 'email' => 'noplan@example.com', 'password' => bcrypt('secret')]);
    $tenant = Tenant::query()->create(['name' => 'NoPlanTenant', 'slug' => 'noplan']);

    app(PermissionRegistrar::class)->forgetCachedPermissions();
    app(PermissionRegistrar::class)->setPermissionsTeamId($tenant->getKey());

    $permission = Permission::query()->create([
        'name' => 'orders.view',
        'guard_name' => 'web',
        'tenant_id' => $tenant->getKey(),
    ]);

    $role = Role::query()->create([
        'name' => 'viewer',
        'guard_name' => 'web',
        'tenant_id' => $tenant->getKey(),
    ]);

    $role->givePermissionTo($permission);
    $user->assignRole($role);

    $result = app(AccessService::class)->checkPermission($user, $tenant, 'orders.view');

    expect($result)->toBeFalse();
});
