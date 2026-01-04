<?php

namespace Haida\FilamentLoyaltyClub\Tests\Feature;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentLoyaltyClub\Models\LoyaltyCustomer;
use Haida\FilamentLoyaltyClub\Models\LoyaltyPointsRule;
use Haida\FilamentLoyaltyClub\Models\LoyaltyWalletLedger;
use Haida\FilamentLoyaltyClub\Services\LoyaltyEventService;
use Haida\FilamentLoyaltyClub\Tests\TestCase;

class LoyaltyIdempotentEarningTest extends TestCase
{
    public function test_event_ingest_is_idempotent(): void
    {
        $tenant = Tenant::create(['name' => 'Tenant', 'slug' => 'tenant']);
        TenantContext::setTenant($tenant);

        $customer = LoyaltyCustomer::create([
            'tenant_id' => $tenant->getKey(),
            'first_name' => 'Test',
        ]);

        LoyaltyPointsRule::create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'Purchase rule',
            'event_type' => 'purchase_completed',
            'status' => 'active',
            'points_type' => 'fixed',
            'points_value' => 100,
        ]);

        $service = app(LoyaltyEventService::class);
        $service->ingest($customer, 'purchase_completed', ['amount' => 200000], 'evt-1', 'orders');
        $service->ingest($customer, 'purchase_completed', ['amount' => 200000], 'evt-1', 'orders');

        $events = $customer->events()->count();
        $ledgers = LoyaltyWalletLedger::query()
            ->where('tenant_id', $tenant->getKey())
            ->where('customer_id', $customer->getKey())
            ->where('type', 'earn')
            ->count();

        $this->assertSame(1, $events);
        $this->assertSame(1, $ledgers);
    }
}
