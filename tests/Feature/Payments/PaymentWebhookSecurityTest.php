<?php

declare(strict_types=1);

namespace Tests\Feature\Payments;

use Filamat\IamSuite\Models\Tenant;
use Haida\FilamentPayments\Models\PaymentProviderConnection;
use Haida\FilamentPayments\Models\PaymentWebhookEvent;
use Haida\FilamentPayments\Providers\ManualExternalTerminalProvider;
use Haida\FilamentPayments\Services\PaymentProviderRegistry;
use Haida\FilamentPayments\Services\WebhookHandler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentWebhookSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_rejects_invalid_signature(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'Tenant Payments A',
            'slug' => 'tenant-payments-a',
            'status' => 'active',
        ]);

        PaymentProviderConnection::query()->create([
            'tenant_id' => $tenant->getKey(),
            'provider_key' => 'manual',
            'display_name' => 'Manual',
            'credentials' => ['webhook_secret' => 'secret-key'],
            'is_active' => true,
        ]);

        app(PaymentProviderRegistry::class)->register(new ManualExternalTerminalProvider());

        $payload = [
            'id' => 'evt-1',
            'status' => 'paid',
        ];

        $raw = json_encode($payload);
        $headers = [
            'X-Timestamp' => (string) time(),
            'X-Signature' => 'invalid',
        ];

        $event = app(WebhookHandler::class)->handle('manual', $headers, $payload, $tenant->getKey(), $raw);

        $this->assertFalse($event->signature_valid);
        $this->assertSame('invalid_signature', $event->status);
        $this->assertDatabaseCount('payments_webhook_events', 1);
    }

    public function test_valid_signature_is_processed_and_idempotent(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'Tenant Payments B',
            'slug' => 'tenant-payments-b',
            'status' => 'active',
        ]);

        PaymentProviderConnection::query()->create([
            'tenant_id' => $tenant->getKey(),
            'provider_key' => 'manual',
            'display_name' => 'Manual',
            'credentials' => ['webhook_secret' => 'secret-key'],
            'is_active' => true,
        ]);

        app(PaymentProviderRegistry::class)->register(new ManualExternalTerminalProvider());

        $payload = [
            'id' => 'evt-2',
            'status' => 'paid',
        ];

        $raw = json_encode($payload);
        $timestamp = (string) time();
        $signature = hash_hmac('sha256', $timestamp.'.'.$raw, 'secret-key');
        $headers = [
            'X-Timestamp' => $timestamp,
            'X-Signature' => $signature,
        ];

        $handler = app(WebhookHandler::class);
        $first = $handler->handle('manual', $headers, $payload, $tenant->getKey(), $raw);
        $second = $handler->handle('manual', $headers, $payload, $tenant->getKey(), $raw);

        $this->assertTrue($first->signature_valid);
        $this->assertSame('processed', $first->status);
        $this->assertSame($first->getKey(), $second->getKey());
        $this->assertSame(1, PaymentWebhookEvent::query()->count());
    }

    public function test_webhook_events_are_unique_per_tenant(): void
    {
        $tenantA = Tenant::query()->create([
            'name' => 'Tenant Payments C',
            'slug' => 'tenant-payments-c',
            'status' => 'active',
        ]);

        $tenantB = Tenant::query()->create([
            'name' => 'Tenant Payments D',
            'slug' => 'tenant-payments-d',
            'status' => 'active',
        ]);

        PaymentProviderConnection::query()->create([
            'tenant_id' => $tenantA->getKey(),
            'provider_key' => 'manual',
            'display_name' => 'Manual',
            'credentials' => ['webhook_secret' => 'secret-key'],
            'is_active' => true,
        ]);

        PaymentProviderConnection::query()->create([
            'tenant_id' => $tenantB->getKey(),
            'provider_key' => 'manual',
            'display_name' => 'Manual',
            'credentials' => ['webhook_secret' => 'secret-key'],
            'is_active' => true,
        ]);

        app(PaymentProviderRegistry::class)->register(new ManualExternalTerminalProvider());

        $payload = [
            'id' => 'evt-shared',
            'status' => 'paid',
        ];

        $raw = json_encode($payload);
        $timestamp = (string) time();
        $signature = hash_hmac('sha256', $timestamp.'.'.$raw, 'secret-key');
        $headers = [
            'X-Timestamp' => $timestamp,
            'X-Signature' => $signature,
        ];

        $handler = app(WebhookHandler::class);
        $first = $handler->handle('manual', $headers, $payload, $tenantA->getKey(), $raw);
        $second = $handler->handle('manual', $headers, $payload, $tenantB->getKey(), $raw);

        $this->assertNotSame($first->getKey(), $second->getKey());
        $this->assertSame(2, PaymentWebhookEvent::query()->withoutGlobalScopes()->count());
        $tenantIds = PaymentWebhookEvent::query()->withoutGlobalScopes()->pluck('tenant_id')->all();
        $this->assertEqualsCanonicalizing([$tenantA->getKey(), $tenantB->getKey()], $tenantIds);
    }
}
