<?php

declare(strict_types=1);

namespace Tests\Feature\Crypto;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentCryptoGateway\Adapters\CryptomusAdapter;
use Haida\FilamentCryptoGateway\Models\CryptoInvoice;
use Haida\FilamentCryptoGateway\Models\CryptoProviderAccount;
use Haida\FilamentCryptoGateway\Models\CryptoWebhookCall;
use Haida\FilamentCryptoGateway\Services\ProviderRegistry;
use Haida\FilamentCryptoGateway\Services\ReconcileService;
use Haida\FilamentCryptoGateway\Services\WebhookProcessor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CryptoReconcileTest extends TestCase
{
    use RefreshDatabase;

    public function test_reconcile_updates_invoice_when_webhook_missing(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'Tenant Crypto B',
            'slug' => 'tenant-crypto-b',
            'status' => 'active',
        ]);
        TenantContext::setTenant($tenant);

        CryptoProviderAccount::query()->create([
            'tenant_id' => $tenant->getKey(),
            'provider' => 'cryptomus',
            'env' => 'prod',
            'merchant_id' => 'merchant-2',
            'api_key_encrypted' => 'cryptomus-key-2',
            'secret_encrypted' => 'cryptomus-secret-2',
            'is_active' => true,
        ]);

        $invoice = CryptoInvoice::query()->create([
            'tenant_id' => $tenant->getKey(),
            'provider' => 'cryptomus',
            'order_id' => 'ORDER-200',
            'external_uuid' => 'inv-200',
            'amount' => 25,
            'currency' => 'USDT',
            'status' => 'unpaid',
        ]);

        app(ProviderRegistry::class)->register(new CryptomusAdapter());

        config(['filament-crypto-gateway.providers.cryptomus.base_url' => 'https://cryptomus.test']);

        Http::fake([
            'https://cryptomus.test/*' => Http::response([
                'result' => [
                    'uuid' => 'inv-200',
                    'status' => 'paid',
                    'amount' => '25',
                    'currency' => 'USDT',
                ],
            ], 200),
        ]);

        $record = app(ReconcileService::class)->run($tenant->getKey());

        $call = CryptoWebhookCall::query()->first();
        if ($call) {
            app(WebhookProcessor::class)->process($call);
        }

        $invoice->refresh();

        $this->assertSame('paid', $invoice->status);
        $this->assertSame('completed', $record->status);
        $this->assertSame(1, CryptoWebhookCall::query()->count());
    }
}
