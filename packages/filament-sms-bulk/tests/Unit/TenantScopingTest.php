<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Tests\Unit;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\SmsBulk\Models\SmsBulkProviderConnection;
use Haida\SmsBulk\Tests\TestCase;

class TenantScopingTest extends TestCase
{
    public function test_models_are_scoped_by_tenant_context(): void
    {
        $tenantA = Tenant::create(['name' => 'A', 'slug' => 'a']);
        $tenantB = Tenant::create(['name' => 'B', 'slug' => 'b']);

        SmsBulkProviderConnection::create([
            'tenant_id' => $tenantA->getKey(),
            'provider' => 'ippanel_edge',
            'display_name' => 'A',
            'status' => 'active',
        ]);

        SmsBulkProviderConnection::create([
            'tenant_id' => $tenantB->getKey(),
            'provider' => 'ippanel_edge',
            'display_name' => 'B',
            'status' => 'active',
        ]);

        TenantContext::setTenant($tenantA);
        $this->assertSame(1, SmsBulkProviderConnection::query()->count());
        $this->assertSame('A', SmsBulkProviderConnection::query()->first()->display_name);

        TenantContext::setTenant($tenantB);
        $this->assertSame(1, SmsBulkProviderConnection::query()->count());
        $this->assertSame('B', SmsBulkProviderConnection::query()->first()->display_name);
    }
}
