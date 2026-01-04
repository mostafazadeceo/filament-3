<?php

declare(strict_types=1);

namespace Tests\Feature\Crypto;

use Filamat\IamSuite\Models\Tenant;
use Haida\FilamentCryptoGateway\Contracts\ProviderAdapterInterface;
use Haida\FilamentCryptoGateway\DTOs\InvoiceCreateData;
use Haida\FilamentCryptoGateway\DTOs\PayoutCreateData;
use Haida\FilamentCryptoGateway\DTOs\ProviderInvoiceData;
use Haida\FilamentCryptoGateway\DTOs\ProviderPayoutData;
use Haida\FilamentCryptoGateway\DTOs\WebhookEventData;
use Haida\FilamentCryptoGateway\Enums\CryptoInvoiceStatus;
use Haida\FilamentCryptoGateway\Enums\CryptoPayoutStatus;
use Haida\FilamentCryptoGateway\Models\CryptoProviderAccount;
use Haida\FilamentCryptoGateway\Services\ProviderRegistry;
use Haida\FilamentCryptoGateway\Services\InvoiceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CryptoInvoiceIdempotencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_id_is_idempotent_per_tenant(): void
    {
        $tenantA = Tenant::query()->create([
            'name' => 'Tenant Crypto Idem A',
            'slug' => 'tenant-crypto-idem-a',
            'status' => 'active',
        ]);

        $tenantB = Tenant::query()->create([
            'name' => 'Tenant Crypto Idem B',
            'slug' => 'tenant-crypto-idem-b',
            'status' => 'active',
        ]);

        CryptoProviderAccount::query()->create([
            'tenant_id' => $tenantA->getKey(),
            'provider' => 'cryptomus',
            'env' => 'prod',
            'merchant_id' => 'merchant-a',
            'api_key_encrypted' => 'key-a',
            'secret_encrypted' => 'secret-a',
            'is_active' => true,
        ]);

        CryptoProviderAccount::query()->create([
            'tenant_id' => $tenantB->getKey(),
            'provider' => 'cryptomus',
            'env' => 'prod',
            'merchant_id' => 'merchant-b',
            'api_key_encrypted' => 'key-b',
            'secret_encrypted' => 'secret-b',
            'is_active' => true,
        ]);

        app(ProviderRegistry::class)->register(new class implements ProviderAdapterInterface {
            public function key(): string
            {
                return 'cryptomus';
            }

            public function supports(): array
            {
                return [];
            }

            public function createInvoice(InvoiceCreateData $data, CryptoProviderAccount $account): ProviderInvoiceData
            {
                return new ProviderInvoiceData(
                    $this->key(),
                    'inv-'.$data->orderId,
                    $data->orderId,
                    $data->amount,
                    $data->currency,
                    $data->toCurrency,
                    $data->network,
                    'address',
                    CryptoInvoiceStatus::Unpaid,
                    false,
                    null,
                    []
                );
            }

            public function getInvoice(string $externalId, CryptoProviderAccount $account): ?ProviderInvoiceData
            {
                return new ProviderInvoiceData(
                    $this->key(),
                    $externalId,
                    'ORDER-X',
                    '0',
                    'USDT',
                    null,
                    null,
                    null,
                    CryptoInvoiceStatus::Unpaid,
                    false,
                    null,
                    []
                );
            }

            public function createPayout(PayoutCreateData $data, CryptoProviderAccount $account): ProviderPayoutData
            {
                return new ProviderPayoutData(
                    $this->key(),
                    '',
                    $data->orderId,
                    $data->amount,
                    $data->currency,
                    $data->network,
                    $data->toAddress,
                    CryptoPayoutStatus::Failed,
                    true,
                    null,
                    'stub',
                    []
                );
            }

            public function getPayout(string $externalId, CryptoProviderAccount $account): ?ProviderPayoutData
            {
                return null;
            }

            public function verifyAndParseWebhook(array $headers, string $rawPayload, string $ip, ?CryptoProviderAccount $account = null): WebhookEventData
            {
                return new WebhookEventData(
                    $this->key(),
                    'evt-stub',
                    'invoice',
                    true,
                    true,
                    'invoice_order',
                    'ORDER-X',
                    CryptoInvoiceStatus::Unpaid,
                    null,
                    null,
                    '0',
                    'USDT',
                    null,
                    false,
                    []
                );
            }
        });

        $service = app(InvoiceService::class);

        $first = $service->create([
            'tenant_id' => $tenantA->getKey(),
            'provider' => 'cryptomus',
            'order_id' => 'ORDER-X',
            'amount' => 10,
            'currency' => 'USDT',
        ]);

        $second = $service->create([
            'tenant_id' => $tenantA->getKey(),
            'provider' => 'cryptomus',
            'order_id' => 'ORDER-X',
            'amount' => 10,
            'currency' => 'USDT',
        ]);

        $third = $service->create([
            'tenant_id' => $tenantB->getKey(),
            'provider' => 'cryptomus',
            'order_id' => 'ORDER-X',
            'amount' => 10,
            'currency' => 'USDT',
        ]);

        $this->assertSame($first->getKey(), $second->getKey());
        $this->assertNotSame($first->getKey(), $third->getKey());
    }
}
