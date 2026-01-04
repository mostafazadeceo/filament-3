<?php

namespace Haida\FilamentLoyaltyClub\Tests\Feature;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentLoyaltyClub\Models\LoyaltyCustomer;
use Haida\FilamentLoyaltyClub\Tests\TestCase;

class LoyaltyTenantIsolationTest extends TestCase
{
    public function test_customers_are_tenant_scoped(): void
    {
        $tenantA = Tenant::create(['name' => 'Tenant A', 'slug' => 'tenant-a']);
        $tenantB = Tenant::create(['name' => 'Tenant B', 'slug' => 'tenant-b']);

        TenantContext::setTenant($tenantA);
        LoyaltyCustomer::create(['tenant_id' => $tenantA->getKey(), 'first_name' => 'A']);

        TenantContext::setTenant($tenantB);
        LoyaltyCustomer::create(['tenant_id' => $tenantB->getKey(), 'first_name' => 'B']);

        TenantContext::setTenant($tenantA);
        $customers = LoyaltyCustomer::query()->get();

        $this->assertCount(1, $customers);
        $this->assertSame('A', $customers->first()->first_name);
    }
}
