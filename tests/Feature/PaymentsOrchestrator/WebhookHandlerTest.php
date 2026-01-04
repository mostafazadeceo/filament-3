<?php

namespace Tests\Feature\PaymentsOrchestrator;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\CommerceOrders\Models\Order;
use Haida\PaymentsOrchestrator\Models\PaymentGatewayConnection;
use Haida\PaymentsOrchestrator\Models\PaymentIntent;
use Haida\PaymentsOrchestrator\Models\PaymentWebhookEvent;
use Haida\PaymentsOrchestrator\Services\PaymentIntentService;
use Haida\SiteBuilderCore\Models\Site;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebhookHandlerTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_is_verified_and_idempotent(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'Tenant Payments',
            'slug' => 'tenant-payments',
            'status' => 'active',
        ]);

        TenantContext::setTenant($tenant);

        $site = Site::query()->create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'Site',
            'slug' => 'site-payments',
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
            'provider_key' => 'dummy',
            'name' => 'Dummy',
            'environment' => 'sandbox',
            'webhook_secret' => 'secret',
            'is_active' => true,
        ]);

        $intent = app(PaymentIntentService::class)->createIntent($order, $connection, [
            'idempotency_key' => 'intent-1',
        ]);

        $payload = json_encode([
            'event_id' => 'evt_1',
            'status' => 'succeeded',
            'intent_id' => $intent->getKey(),
            'order_id' => $order->getKey(),
            'amount' => 200,
            'currency' => 'IRR',
            'reference' => $intent->provider_reference,
        ], JSON_UNESCAPED_UNICODE);

        $timestamp = time();
        $signature = hash_hmac('sha256', $timestamp . '.' . $payload, 'secret');

        $response = $this->call('POST', '/api/v1/commerce-payments/webhooks/dummy', [], [], [], [
            'HTTP_X_TENANT_ID' => $tenant->getKey(),
            'HTTP_X_TIMESTAMP' => $timestamp,
            'HTTP_X_SIGNATURE' => $signature,
        ], $payload);

        $response->assertStatus(200);

        $order->refresh();
        $intent->refresh();

        $this->assertSame('paid', $order->payment_status);
        $this->assertSame('processing', $order->status);
        $this->assertSame('succeeded', $intent->status);
        $this->assertSame(1, $order->payments()->count());
        $this->assertSame(1, PaymentWebhookEvent::query()->count());

        $response = $this->call('POST', '/api/v1/commerce-payments/webhooks/dummy', [], [], [], [
            'HTTP_X_TENANT_ID' => $tenant->getKey(),
            'HTTP_X_TIMESTAMP' => $timestamp,
            'HTTP_X_SIGNATURE' => $signature,
        ], $payload);

        $response->assertStatus(200);
        $this->assertSame(1, PaymentWebhookEvent::query()->count());
        $this->assertSame(1, $order->payments()->count());
    }
}
