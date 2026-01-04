<?php

namespace Haida\FilamentThreeCx\Tests\Feature;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentThreeCx\Models\ThreeCxInstance;
use Haida\FilamentThreeCx\Tests\TestCase;
use Illuminate\Support\Str;

class ThreeCxTenancyTest extends TestCase
{
    public function test_instances_are_tenant_scoped(): void
    {
        $tenantA = $this->createTenant('tenant-a');
        $tenantB = $this->createTenant('tenant-b');

        TenantContext::setTenant($tenantA);
        ThreeCxInstance::create([
            'tenant_id' => $tenantA->getKey(),
            'name' => 'A',
            'base_url' => 'https://a.local',
            'verify_tls' => true,
        ]);

        TenantContext::setTenant($tenantB);
        ThreeCxInstance::create([
            'tenant_id' => $tenantB->getKey(),
            'name' => 'B',
            'base_url' => 'https://b.local',
            'verify_tls' => true,
        ]);

        TenantContext::setTenant($tenantA);
        $this->assertCount(1, ThreeCxInstance::all());
        $this->assertSame('A', ThreeCxInstance::query()->first()->name);
    }

    protected function createTenant(string $slug): Tenant
    {
        return Tenant::create([
            'name' => Str::title(str_replace('-', ' ', $slug)),
            'slug' => $slug,
        ]);
    }
}
