<?php

namespace Haida\FilamentLoyaltyClub\Tests\Feature;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentLoyaltyClub\Models\LoyaltyCampaign;
use Haida\FilamentLoyaltyClub\Models\LoyaltyCampaignVariant;
use Haida\FilamentLoyaltyClub\Models\LoyaltyCustomer;
use Haida\FilamentLoyaltyClub\Models\LoyaltyCustomerSegment;
use Haida\FilamentLoyaltyClub\Models\LoyaltySegment;
use Haida\FilamentLoyaltyClub\Services\LoyaltyCampaignService;
use Haida\FilamentLoyaltyClub\Tests\TestCase;

class LoyaltyCampaignOfferTest extends TestCase
{
    public function test_offers_are_resolved_for_segment_customer(): void
    {
        $tenant = Tenant::create(['name' => 'Tenant', 'slug' => 'tenant']);
        TenantContext::setTenant($tenant);

        $customer = LoyaltyCustomer::create([
            'tenant_id' => $tenant->getKey(),
            'first_name' => 'Customer',
        ]);

        $segment = LoyaltySegment::create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'Active',
            'type' => 'rule',
            'status' => 'active',
        ]);

        LoyaltyCustomerSegment::create([
            'tenant_id' => $tenant->getKey(),
            'segment_id' => $segment->getKey(),
            'customer_id' => $customer->getKey(),
            'source' => 'rule',
            'assigned_at' => now(),
        ]);

        $campaign = LoyaltyCampaign::create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'Campaign',
            'status' => 'active',
        ]);

        $campaign->segments()->attach($segment->getKey(), ['tenant_id' => $tenant->getKey()]);

        LoyaltyCampaignVariant::create([
            'tenant_id' => $tenant->getKey(),
            'campaign_id' => $campaign->getKey(),
            'name' => 'A',
            'channel' => 'sms',
            'weight' => 100,
            'status' => 'active',
            'content' => ['headline' => 'Offer'],
        ]);

        $service = app(LoyaltyCampaignService::class);
        $offers = $service->getOffersForCustomer($customer);

        $this->assertNotEmpty($offers);
        $this->assertSame('sms', $offers[0]['channel']);
    }
}
