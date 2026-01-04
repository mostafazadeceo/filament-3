<?php

namespace Haida\FilamentCryptoCore\Tests\Unit;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentCryptoCore\Models\CryptoAccount;
use Haida\FilamentCryptoCore\Services\LedgerService;
use Haida\FilamentCryptoCore\Tests\TestCase;
use Illuminate\Validation\ValidationException;

class LedgerServiceTest extends TestCase
{
    public function test_it_posts_balanced_ledger_entries(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'Tenant A',
            'slug' => 'tenant-a',
            'status' => 'active',
        ]);

        TenantContext::setTenant($tenant);

        $debitAccount = CryptoAccount::query()->create([
            'tenant_id' => $tenant->getKey(),
            'code' => 'CRYPTO_CLEARING',
            'name_fa' => 'کلیرینگ رمزارز',
            'type' => 'asset',
        ]);

        $creditAccount = CryptoAccount::query()->create([
            'tenant_id' => $tenant->getKey(),
            'code' => 'MERCHANT_PAYABLE',
            'name_fa' => 'بدهی پذیرنده',
            'type' => 'liability',
        ]);

        $ledger = app(LedgerService::class)->postLedger([
            'tenant_id' => $tenant->getKey(),
            'ref_type' => 'invoice',
            'ref_id' => 'INV-1',
            'occurred_at' => now(),
            'description' => 'تست دفترکل',
        ], [
            [
                'account_id' => $debitAccount->getKey(),
                'debit' => 100,
                'credit' => 0,
                'currency' => 'USDT',
            ],
            [
                'account_id' => $creditAccount->getKey(),
                'debit' => 0,
                'credit' => 100,
                'currency' => 'USDT',
            ],
        ]);

        $this->assertCount(2, $ledger->entries()->get());
    }

    public function test_it_rejects_unbalanced_entries(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'Tenant B',
            'slug' => 'tenant-b',
            'status' => 'active',
        ]);

        TenantContext::setTenant($tenant);

        $debitAccount = CryptoAccount::query()->create([
            'tenant_id' => $tenant->getKey(),
            'code' => 'CRYPTO_CLEARING',
            'name_fa' => 'کلیرینگ رمزارز',
            'type' => 'asset',
        ]);

        $creditAccount = CryptoAccount::query()->create([
            'tenant_id' => $tenant->getKey(),
            'code' => 'MERCHANT_PAYABLE',
            'name_fa' => 'بدهی پذیرنده',
            'type' => 'liability',
        ]);

        $this->expectException(ValidationException::class);

        app(LedgerService::class)->postLedger([
            'tenant_id' => $tenant->getKey(),
            'ref_type' => 'invoice',
            'ref_id' => 'INV-2',
            'occurred_at' => now(),
        ], [
            [
                'account_id' => $debitAccount->getKey(),
                'debit' => 100,
                'credit' => 0,
                'currency' => 'USDT',
            ],
            [
                'account_id' => $creditAccount->getKey(),
                'debit' => 0,
                'credit' => 90,
                'currency' => 'USDT',
            ],
        ]);
    }
}
