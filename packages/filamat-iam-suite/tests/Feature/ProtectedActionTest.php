<?php

declare(strict_types=1);

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Services\ProtectedActionService;
use Filamat\IamSuite\Tests\Fixtures\User;

it('issues and validates protected action token', function () {
    $tenant = Tenant::query()->create(['name' => 'Secure Tenant', 'slug' => 'secure']);
    $user = User::query()->create(['name' => 'Secure User', 'email' => 'secure@example.com', 'password' => bcrypt('secret')]);

    $service = app(ProtectedActionService::class);
    $token = $service->issueWithPassword($user, 'iam.impersonate', 'secret', $tenant);

    $service->requireToken($user, 'iam.impersonate', $tenant, $token);

    expect(true)->toBeTrue();
});
