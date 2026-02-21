<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Tests\Unit;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\SmsBulk\Models\SmsBulkProviderConnection;
use Haida\SmsBulk\Services\Campaign\CampaignPricingService;
use Haida\SmsBulk\Tests\TestCase;
use Illuminate\Support\Facades\Http;

class CampaignPricingServiceTest extends TestCase
{
    public function test_estimator_uses_provider_calculate_price_when_available(): void
    {
        Http::fake([
            '*' => Http::response([
                'data' => ['mci_price' => 1000, 'other_price' => 1500, 'parts' => 2],
                'meta' => ['status' => true],
            ], 200),
        ]);

        $tenant = Tenant::create(['name' => 'Tenant', 'slug' => 'tenant']);
        TenantContext::setTenant($tenant);

        $connection = SmsBulkProviderConnection::create([
            'tenant_id' => $tenant->getKey(),
            'provider' => 'ippanel_edge',
            'display_name' => 'edge',
            'encrypted_token' => 'token',
            'base_url_override' => 'https://edge.ippanel.com/v1',
            'status' => 'active',
        ]);

        $service = app(CampaignPricingService::class);
        $result = $service->estimate($connection, ['sender' => '3000505', 'message' => 'hello'], 3);

        $this->assertSame(9000.0, (float) $result['estimate']);
    }

    public function test_estimator_falls_back_to_local_estimation_on_provider_error(): void
    {
        Http::fake([
            '*' => Http::response(['data' => null, 'meta' => ['status' => false, 'message' => 'error']], 500),
        ]);

        $tenant = Tenant::create(['name' => 'Tenant2', 'slug' => 'tenant2']);
        TenantContext::setTenant($tenant);

        $connection = SmsBulkProviderConnection::create([
            'tenant_id' => $tenant->getKey(),
            'provider' => 'ippanel_edge',
            'display_name' => 'edge',
            'encrypted_token' => 'token',
            'base_url_override' => 'https://edge.ippanel.com/v1',
            'status' => 'active',
        ]);

        $service = app(CampaignPricingService::class);
        $result = $service->estimate($connection, ['message' => str_repeat('ا', 71)], 2);

        $this->assertGreaterThan(0, (float) $result['estimate']);
        $this->assertTrue((bool) ($result['pricing_snapshot']['fallback'] ?? false));
    }
}
