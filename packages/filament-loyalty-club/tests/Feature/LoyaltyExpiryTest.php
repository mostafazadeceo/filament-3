<?php

namespace Haida\FilamentLoyaltyClub\Tests\Feature;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentLoyaltyClub\Models\LoyaltyCustomer;
use Haida\FilamentLoyaltyClub\Models\LoyaltyPointsBucket;
use Haida\FilamentLoyaltyClub\Services\LoyaltyExpiryService;
use Haida\FilamentLoyaltyClub\Services\LoyaltyLedgerService;
use Haida\FilamentLoyaltyClub\Tests\TestCase;

class LoyaltyExpiryTest extends TestCase
{
    public function test_expired_points_are_deducted(): void
    {
        $tenant = Tenant::create(['name' => 'Tenant', 'slug' => 'tenant']);
        TenantContext::setTenant($tenant);

        $customer = LoyaltyCustomer::create([
            'tenant_id' => $tenant->getKey(),
            'first_name' => 'Customer',
        ]);

        $ledgerService = app(LoyaltyLedgerService::class);
        $ledger = $ledgerService->creditPoints($customer, 50, 'earn-expire');

        LoyaltyPointsBucket::query()
            ->where('ledger_id', $ledger->getKey())
            ->update(['expires_at' => now()->subDay()]);

        $expiryService = app(LoyaltyExpiryService::class);
        $expired = $expiryService->expirePoints();

        $account = $ledgerService->getOrCreateAccount($customer);
        $this->assertSame(50, $expired);
        $this->assertSame(0, (int) $account->points_balance);
    }
}
