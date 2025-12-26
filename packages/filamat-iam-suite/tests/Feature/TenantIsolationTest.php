<?php

declare(strict_types=1);

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Models\Wallet;
use Filamat\IamSuite\Support\TenantContext;
use Filamat\IamSuite\Tests\Fixtures\User;

it('scopes tenant models by tenant context', function () {
    $user = User::query()->create(['name' => 'Test', 'email' => 't@example.com', 'password' => bcrypt('secret')]);

    $tenantA = Tenant::query()->create(['name' => 'Tenant A', 'slug' => 'a']);
    $tenantB = Tenant::query()->create(['name' => 'Tenant B', 'slug' => 'b']);

    Wallet::query()->create(['tenant_id' => $tenantA->getKey(), 'user_id' => $user->getKey(), 'currency' => 'irr', 'balance' => 10, 'status' => 'active']);
    Wallet::query()->create(['tenant_id' => $tenantB->getKey(), 'user_id' => $user->getKey(), 'currency' => 'irr', 'balance' => 20, 'status' => 'active']);

    TenantContext::setTenant($tenantA);
    expect(Wallet::query()->count())->toBe(1);

    TenantContext::setTenant($tenantB);
    expect(Wallet::query()->count())->toBe(1);
});
