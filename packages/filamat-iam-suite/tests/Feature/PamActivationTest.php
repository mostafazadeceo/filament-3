<?php

declare(strict_types=1);

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Services\PrivilegeElevationService;
use Filamat\IamSuite\Services\PrivilegeEligibilityService;
use Filamat\IamSuite\Tests\Fixtures\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

it('activates and expires pam roles', function () {
    $tenant = Tenant::query()->create(['name' => 'PAM Tenant', 'slug' => 'pam-tenant']);
    $user = User::query()->create(['name' => 'Pam User', 'email' => 'pam@example.com', 'password' => bcrypt('secret')]);

    app(PermissionRegistrar::class)->forgetCachedPermissions();
    app(PermissionRegistrar::class)->setPermissionsTeamId($tenant->getKey());

    $role = Role::query()->create([
        'name' => 'privileged',
        'guard_name' => 'web',
        'tenant_id' => $tenant->getKey(),
    ]);

    app(PrivilegeEligibilityService::class)->grant($tenant, $user, $role, $user, 'grant');

    $request = app(PrivilegeElevationService::class)->request(
        $tenant,
        $user,
        $role,
        15,
        $user,
        'need access',
        'T-1'
    );

    app(PrivilegeElevationService::class)->approve($request, $user, 'ok');

    $activation = app(PrivilegeElevationService::class)->activate(
        $tenant,
        $user,
        $role,
        $request,
        $user,
        'activate',
        'T-1',
        now()->addMinutes(1)
    );

    app(PermissionRegistrar::class)->setPermissionsTeamId($tenant->getKey());
    expect($user->hasRole($role->name))->toBeTrue();

    $activation->update(['expires_at' => now()->subMinute()]);
    app(PrivilegeElevationService::class)->expireDueActivations($tenant);

    $user->refresh();
    app(PermissionRegistrar::class)->setPermissionsTeamId($tenant->getKey());
    expect($user->hasRole($role->name))->toBeFalse();
});
