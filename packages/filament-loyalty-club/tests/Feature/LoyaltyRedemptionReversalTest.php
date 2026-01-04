<?php

namespace Haida\FilamentLoyaltyClub\Tests\Feature;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentLoyaltyClub\Models\LoyaltyCustomer;
use Haida\FilamentLoyaltyClub\Services\LoyaltyLedgerService;
use Haida\FilamentLoyaltyClub\Tests\TestCase;

class LoyaltyRedemptionReversalTest extends TestCase
{
    public function test_reversal_restores_points(): void
    {
        $tenant = Tenant::create(['name' => 'Tenant', 'slug' => 'tenant']);
        TenantContext::setTenant($tenant);

        $customer = LoyaltyCustomer::create([
            'tenant_id' => $tenant->getKey(),
            'first_name' => 'Customer',
        ]);

        $service = app(LoyaltyLedgerService::class);
        $service->creditPoints($customer, 100, 'earn-1');
        $burn = $service->debitPoints($customer, 40, 'burn-1');

        $account = $service->getOrCreateAccount($customer);
        $this->assertSame(60, (int) $account->points_balance);

        $service->reverseLedger($burn, 'reverse-1');
        $account->refresh();

        $this->assertSame(100, (int) $account->points_balance);
    }
}
