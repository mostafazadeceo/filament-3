<?php

declare(strict_types=1);

namespace Tests\Feature\Crypto;

use Haida\FilamentCryptoGateway\Contracts\ProviderAdapterInterface;
use Haida\FilamentCryptoGateway\DTOs\InvoiceCreateData;
use Haida\FilamentCryptoGateway\DTOs\PayoutCreateData;
use Haida\FilamentCryptoGateway\DTOs\ProviderInvoiceData;
use Haida\FilamentCryptoGateway\DTOs\ProviderPayoutData;
use Haida\FilamentCryptoGateway\DTOs\WebhookEventData;
use Haida\FilamentCryptoGateway\Enums\CryptoInvoiceStatus;
use Haida\FilamentCryptoGateway\Enums\CryptoPayoutStatus;
use Haida\FilamentCryptoGateway\Models\CryptoPayout;
use Haida\FilamentCryptoGateway\Models\CryptoPayoutDestination;
use Haida\FilamentCryptoGateway\Models\CryptoProviderAccount;
use Haida\FilamentCryptoGateway\Services\ProviderRegistry;
use Laravel\Sanctum\Sanctum;

class CryptoPayoutApiTest extends CryptoApiTestCase
{
    public function test_can_approve_payout_via_api(): void
    {
        $tenant = $this->createTenant('Tenant Crypto Approve');

        CryptoProviderAccount::query()->create([
            'tenant_id' => $tenant->getKey(),
            'provider' => 'cryptomus',
            'env' => 'prod',
            'merchant_id' => 'merchant-approve',
            'api_key_encrypted' => 'cryptomus-key',
            'secret_encrypted' => 'cryptomus-secret',
            'is_active' => true,
        ]);

        CryptoPayoutDestination::query()->create([
            'tenant_id' => $tenant->getKey(),
            'label' => 'Main Wallet',
            'address' => 'TRON-ADDR',
            'currency' => 'USDT',
            'network' => 'TRC20',
            'status' => 'active',
        ]);

        $payout = CryptoPayout::query()->create([
            'tenant_id' => $tenant->getKey(),
            'provider' => 'cryptomus',
            'order_id' => 'ORDER-APPROVE',
            'to_address' => 'TRON-ADDR',
            'amount' => 5,
            'currency' => 'USDT',
            'network' => 'TRC20',
            'fee' => 0,
            'status' => 'pending_approval',
            'is_final' => false,
        ]);

        app(ProviderRegistry::class)->register(new class implements ProviderAdapterInterface {
            public function key(): string
            {
                return 'cryptomus';
            }

            public function supports(): array
            {
                return ['payouts' => true];
            }

            public function createInvoice(InvoiceCreateData $data, CryptoProviderAccount $account): ProviderInvoiceData
            {
                return new ProviderInvoiceData(
                    $this->key(),
                    'stub',
                    $data->orderId,
                    $data->amount,
                    $data->currency,
                    $data->toCurrency,
                    $data->network,
                    null,
                    CryptoInvoiceStatus::Pending,
                    false,
                    null,
                    []
                );
            }

            public function getInvoice(string $externalId, CryptoProviderAccount $account): ?ProviderInvoiceData
            {
                return null;
            }

            public function createPayout(PayoutCreateData $data, CryptoProviderAccount $account): ProviderPayoutData
            {
                return new ProviderPayoutData(
                    $this->key(),
                    'payout-'.$data->orderId,
                    $data->orderId,
                    $data->amount,
                    $data->currency,
                    $data->network,
                    $data->toAddress,
                    CryptoPayoutStatus::Completed,
                    true,
                    'tx-'.$data->orderId,
                    null,
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
                    'evt-'.$rawPayload,
                    'payout',
                    true,
                    true,
                    'payout_order',
                    'ORDER-APPROVE',
                    null,
                    CryptoPayoutStatus::Completed,
                    null,
                    '0',
                    'USDT',
                    0,
                    true,
                    []
                );
            }
        });

        $user = $this->createUserWithPermissions($tenant, [
            'crypto.payouts.approve',
            'crypto.payouts.view',
        ]);

        Sanctum::actingAs($user, [
            'crypto.payouts.approve',
            'crypto.payouts.view',
            'tenant:'.$tenant->getKey(),
        ]);

        $response = $this->postJson('/api/v1/crypto/payouts/'.$payout->getKey().'/approve', [
            'note' => 'ok',
        ], [
            'X-Tenant-ID' => $tenant->getKey(),
        ]);

        $response->assertOk();
        $response->assertJsonPath('data.status', 'completed');
    }

    public function test_can_reject_payout_via_api(): void
    {
        $tenant = $this->createTenant('Tenant Crypto Reject');

        CryptoProviderAccount::query()->create([
            'tenant_id' => $tenant->getKey(),
            'provider' => 'cryptomus',
            'env' => 'prod',
            'merchant_id' => 'merchant-reject',
            'api_key_encrypted' => 'cryptomus-key',
            'secret_encrypted' => 'cryptomus-secret',
            'is_active' => true,
        ]);

        CryptoPayoutDestination::query()->create([
            'tenant_id' => $tenant->getKey(),
            'label' => 'Backup Wallet',
            'address' => 'TRON-ADDR-2',
            'currency' => 'USDT',
            'network' => 'TRC20',
            'status' => 'active',
        ]);

        $payout = CryptoPayout::query()->create([
            'tenant_id' => $tenant->getKey(),
            'provider' => 'cryptomus',
            'order_id' => 'ORDER-REJECT',
            'to_address' => 'TRON-ADDR-2',
            'amount' => 2,
            'currency' => 'USDT',
            'network' => 'TRC20',
            'fee' => 0,
            'status' => 'pending_approval',
            'is_final' => false,
        ]);

        $user = $this->createUserWithPermissions($tenant, [
            'crypto.payouts.approve',
        ]);

        Sanctum::actingAs($user, [
            'crypto.payouts.approve',
            'tenant:'.$tenant->getKey(),
        ]);

        $response = $this->postJson('/api/v1/crypto/payouts/'.$payout->getKey().'/reject', [
            'note' => 'reject',
        ], [
            'X-Tenant-ID' => $tenant->getKey(),
        ]);

        $response->assertOk();
        $response->assertJsonPath('data.status', 'cancelled');
    }
}
