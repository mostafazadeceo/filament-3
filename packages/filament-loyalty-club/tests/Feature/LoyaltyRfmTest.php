<?php

namespace Haida\FilamentLoyaltyClub\Tests\Feature;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentLoyaltyClub\Models\LoyaltyCustomer;
use Haida\FilamentLoyaltyClub\Services\LoyaltyMetricsService;
use Haida\FilamentLoyaltyClub\Tests\TestCase;

class LoyaltyRfmTest extends TestCase
{
    public function test_rfm_metrics_are_computed(): void
    {
        $tenant = Tenant::create(['name' => 'Tenant', 'slug' => 'tenant']);
        TenantContext::setTenant($tenant);

        $customer = LoyaltyCustomer::create([
            'tenant_id' => $tenant->getKey(),
            'first_name' => 'Customer',
        ]);

        $service = app(LoyaltyMetricsService::class);
        $metric = $service->recordPurchase($customer, 1500000);

        $this->assertNotNull($metric->rfm_score);
        $this->assertSame(1, (int) $metric->purchase_count);
    }
}
