<?php

namespace Tests\Feature\AccountingIr;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_scope_limits_accounting_data(): void
    {
        $tenantA = Tenant::query()->create([
            'name' => 'Tenant A',
            'slug' => 'tenant-a',
        ]);
        $tenantB = Tenant::query()->create([
            'name' => 'Tenant B',
            'slug' => 'tenant-b',
        ]);

        TenantContext::setTenant($tenantA);
        AccountingCompany::query()->create(['name' => 'Alpha']);

        TenantContext::setTenant($tenantB);
        AccountingCompany::query()->create(['name' => 'Beta']);

        TenantContext::setTenant($tenantA);
        $visible = AccountingCompany::query()->pluck('name')->all();

        $this->assertSame(['Alpha'], $visible);
    }
}
