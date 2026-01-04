<?php

namespace Tests\Feature\PaymentsOrchestrator;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\CommerceOrders\Models\Order;
use Haida\PaymentsOrchestrator\Models\PaymentGatewayConnection;
use Haida\PaymentsOrchestrator\Services\PaymentIntentService;
use Haida\SiteBuilderCore\Models\Site;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class HmacGatewayAdapterTest extends TestCase
{
    use RefreshDatabase;

    public function test_hmac_adapter_creates_intent(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'Tenant HMAC',
            'slug' => 'tenant-hmac',
            'status' => 'active',
        ]);

        TenantContext::setTenant($tenant);

        $site = Site::query()->create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'Site',
            'slug' => 'site-hmac',
            'type' => 'store',
            'status' => 'published',
            'currency' => 'IRR',
        ]);

        $order = Order::query()->create([
            'tenant_id' => $tenant->getKey(),
            'site_id' => $site->getKey(),
            'status' => 'pending',
            'payment_status' => 'pending',
            'currency' => 'IRR',
            'subtotal' => 200,
            'total' => 200,
            'placed_at' => now(),
        ]);

        $connection = PaymentGatewayConnection::query()->create([
            'tenant_id' => $tenant->getKey(),
            'provider_key' => 'hmac',
            'name' => 'HMAC Gateway',
            'environment' => 'sandbox',
            'api_key' => 'api-key',
            'api_secret' => 'api-secret',
            'webhook_secret' => 'webhook-secret',
            'settings' => [
                'base_url' => 'https://gateway.test',
                'create_path' => '/payment-intents',
            ],
            'is_active' => true,
        ]);

        Http::fake([
            'https://gateway.test/payment-intents' => Http::response([
                'reference' => 'ref-123',
                'redirect_url' => 'https://gateway.test/redirect/123',
                'status' => 'requires_action',
            ], 200),
        ]);

        $intent = app(PaymentIntentService::class)->createIntent($order, $connection, [
            'idempotency_key' => 'intent-hmac-1',
            'return_url' => 'https://example.test/return',
        ]);

        $this->assertSame('ref-123', $intent->provider_reference);
        $this->assertSame('requires_action', $intent->status);
        $this->assertSame('https://gateway.test/redirect/123', $intent->redirect_url);
    }
}
