<?php

declare(strict_types=1);

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Models\Wallet;
use Filamat\IamSuite\Models\WalletTransaction;
use Filamat\IamSuite\Services\WalletService;
use Filamat\IamSuite\Tests\Fixtures\User;

it('handles idempotent wallet credits', function () {
    $user = User::query()->create(['name' => 'User', 'email' => 'wallet@example.com', 'password' => bcrypt('secret')]);
    $tenant = Tenant::query()->create(['name' => 'Tenant', 'slug' => 'wallet']);

    $wallet = Wallet::query()->create([
        'tenant_id' => $tenant->getKey(),
        'user_id' => $user->getKey(),
        'currency' => 'irr',
        'balance' => 0,
        'status' => 'active',
    ]);

    $service = app(WalletService::class);

    $service->credit($wallet, 10, 'credit-1');
    $service->credit($wallet, 10, 'credit-1');

    expect(WalletTransaction::query()->where('wallet_id', $wallet->getKey())->count())->toBe(1);
    expect($wallet->fresh()->balance)->toBe('10.0000');
});

it('prevents debit when balance is insufficient', function () {
    $user = User::query()->create(['name' => 'User2', 'email' => 'wallet2@example.com', 'password' => bcrypt('secret')]);
    $tenant = Tenant::query()->create(['name' => 'Tenant2', 'slug' => 'wallet2']);

    $wallet = Wallet::query()->create([
        'tenant_id' => $tenant->getKey(),
        'user_id' => $user->getKey(),
        'currency' => 'irr',
        'balance' => 0,
        'status' => 'active',
    ]);

    $service = app(WalletService::class);

    $service->credit($wallet, 5, 'credit-2');

    expect(fn () => $service->debit($wallet, 10, 'debit-1'))
        ->toThrow(RuntimeException::class);
});
