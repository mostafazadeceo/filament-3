<?php

declare(strict_types=1);

namespace Tests\Feature\Crypto;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentCryptoCore\Models\CryptoLedger;
use Haida\FilamentCryptoGateway\Adapters\CryptomusAdapter;
use Haida\FilamentCryptoGateway\Models\CryptoInvoice;
use Haida\FilamentCryptoGateway\Models\CryptoProviderAccount;
use Haida\FilamentCryptoGateway\Models\CryptoWebhookCall;
use Haida\FilamentCryptoGateway\Services\ProviderRegistry;
use Haida\FilamentCryptoGateway\Services\WebhookIngestionService;
use Haida\FilamentCryptoGateway\Services\WebhookProcessor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CryptoWebhookProcessingTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_is_idempotent_and_records_ledger(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'Tenant Crypto A',
            'slug' => 'tenant-crypto-a',
            'status' => 'active',
        ]);
        TenantContext::setTenant($tenant);

        CryptoProviderAccount::query()->create([
            'tenant_id' => $tenant->getKey(),
            'provider' => 'cryptomus',
            'env' => 'prod',
            'merchant_id' => 'merchant-1',
            'api_key_encrypted' => 'cryptomus-key',
            'secret_encrypted' => 'cryptomus-secret',
            'is_active' => true,
        ]);

        $invoice = CryptoInvoice::query()->create([
            'tenant_id' => $tenant->getKey(),
            'provider' => 'cryptomus',
            'order_id' => 'ORDER-100',
            'external_uuid' => 'inv-100',
            'amount' => 10,
            'currency' => 'USDT',
            'status' => 'unpaid',
        ]);

        app(ProviderRegistry::class)->register(new CryptomusAdapter());

        $payloadForSign = [
            'uuid' => 'inv-100',
            'order_id' => 'ORDER-100',
            'status' => 'paid',
            'amount' => '10',
            'currency' => 'USDT',
        ];
        $rawForSign = json_encode($payloadForSign, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '';
        $sign = md5(base64_encode($rawForSign).'cryptomus-key');
        $payload = array_merge($payloadForSign, ['sign' => $sign]);
        $raw = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '';

        $service = app(WebhookIngestionService::class);
        $first = $service->ingest('cryptomus', [], $raw, '91.227.144.54', $tenant->getKey());
        $second = $service->ingest('cryptomus', [], $raw, '91.227.144.54', $tenant->getKey());

        app(WebhookProcessor::class)->process($first->refresh());

        $this->assertSame($first->getKey(), $second->getKey());
        $this->assertSame(1, CryptoWebhookCall::query()->count());

        $invoice->refresh();
        $this->assertSame('paid', $invoice->status);

        $ledger = CryptoLedger::query()
            ->where('ref_type', 'crypto_invoice')
            ->where('ref_id', (string) $invoice->getKey())
            ->first();

        $this->assertNotNull($ledger);

        $entries = $ledger->entries()->get();
        $debit = round((float) $entries->sum('debit'), 8);
        $credit = round((float) $entries->sum('credit'), 8);
        $this->assertSame($debit, $credit);
    }
}
