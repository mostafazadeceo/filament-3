<?php

namespace Haida\FilamentLoyaltyClub\Tests\Feature;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentLoyaltyClub\Models\LoyaltyCustomer;
use Haida\FilamentLoyaltyClub\Models\LoyaltyFraudSignal;
use Haida\FilamentLoyaltyClub\Models\LoyaltyReferralProgram;
use Haida\FilamentLoyaltyClub\Services\LoyaltyReferralService;
use Haida\FilamentLoyaltyClub\Tests\TestCase;

class LoyaltyReferralFraudTest extends TestCase
{
    public function test_self_referral_is_flagged(): void
    {
        $tenant = Tenant::create(['name' => 'Tenant', 'slug' => 'tenant']);
        TenantContext::setTenant($tenant);

        $customer = LoyaltyCustomer::create([
            'tenant_id' => $tenant->getKey(),
            'first_name' => 'Referrer',
            'email' => 'user@example.com',
        ]);

        $program = LoyaltyReferralProgram::create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'Referral',
            'status' => 'active',
            'qualification_event' => 'purchase_completed',
        ]);

        $service = app(LoyaltyReferralService::class);
        $referral = $service->createReferral($program, $customer, [
            'referee_email' => 'user@example.com',
        ]);

        $this->assertSame('flagged', $referral->status);
        $this->assertTrue(LoyaltyFraudSignal::query()->where('subject_id', $referral->getKey())->exists());
    }
}
