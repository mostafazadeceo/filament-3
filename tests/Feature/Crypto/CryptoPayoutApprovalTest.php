<?php

declare(strict_types=1);

namespace Tests\Feature\Crypto;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Models\Subscription;
use Filamat\IamSuite\Models\SubscriptionPlan;
use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentCryptoCore\Models\CryptoLedger;
use Haida\FilamentCryptoGateway\Contracts\ProviderAdapterInterface;
use Haida\FilamentCryptoGateway\DTOs\InvoiceCreateData;
use Haida\FilamentCryptoGateway\DTOs\PayoutCreateData;
use Haida\FilamentCryptoGateway\DTOs\ProviderInvoiceData;
use Haida\FilamentCryptoGateway\DTOs\ProviderPayoutData;
use Haida\FilamentCryptoGateway\DTOs\WebhookEventData;
use Haida\FilamentCryptoGateway\Enums\CryptoInvoiceStatus;
use Haida\FilamentCryptoGateway\Enums\CryptoPayoutStatus;
use Haida\FilamentCryptoGateway\Models\CryptoPayoutDestination;
use Haida\FilamentCryptoGateway\Models\CryptoProviderAccount;
use Haida\FilamentCryptoGateway\Services\PayoutService;
use Haida\FilamentCryptoGateway\Services\ProviderRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CryptoPayoutApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_payout_requires_approval_and_whitelist(): void
    {
        config([
            'filament-crypto-gateway.payouts.require_approval' => true,
            'filament-crypto-gateway.payouts.whitelist.enabled' => true,
        ]);

        $tenant = Tenant::query()->create([
            'name' => 'Tenant Crypto Payout',
            'slug' => 'tenant-crypto-payout',
            'status' => 'active',
        ]);
        TenantContext::setTenant($tenant);

        $plan = SubscriptionPlan::query()->create([
            'tenant_id' => $tenant->getKey(),
            'code' => 'crypto-payouts',
            'scope' => 'tenant',
            'name' => 'Crypto Payouts',
            'price' => 0,
            'currency' => 'irr',
            'period_days' => 30,
            'trial_days' => 0,
            'features' => [
                'crypto_features' => [
                    'crypto.payouts' => true,
                ],
            ],
            'is_active' => true,
        ]);

        Subscription::query()->create([
            'tenant_id' => $tenant->getKey(),
            'plan_id' => $plan->getKey(),
            'status' => 'active',
            'provider' => 'test',
            'provider_ref' => 'test',
        ]);

        CryptoProviderAccount::query()->create([
            'tenant_id' => $tenant->getKey(),
            'provider' => 'cryptomus',
            'env' => 'prod',
            'merchant_id' => 'merchant-payout',
            'api_key_encrypted' => 'cryptomus-key',
            'secret_encrypted' => 'cryptomus-secret',
            'is_active' => true,
        ]);

        $destination = CryptoPayoutDestination::query()->create([
            'tenant_id' => $tenant->getKey(),
            'label' => 'Main Wallet',
            'address' => 'TRON-ADDR',
            'currency' => 'USDT',
            'network' => 'TRC20',
            'status' => 'active',
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
                    'ORDER-PAYOUT',
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

        $service = app(PayoutService::class);

        $payout = $service->create([
            'tenant_id' => $tenant->getKey(),
            'provider' => 'cryptomus',
            'order_id' => 'ORDER-PAYOUT',
            'to_address' => 'TRON-ADDR',
            'amount' => 5,
            'currency' => 'USDT',
            'network' => 'TRC20',
        ]);

        $this->assertSame('pending_approval', $payout->status);

        $approved = $service->approve($payout);

        $this->assertSame('completed', $approved->status);
        $this->assertNotNull($approved->approved_at);

        $destination->refresh();
        $this->assertNotNull($destination->last_used_at);

        $ledger = CryptoLedger::query()
            ->where('ref_type', 'crypto_payout')
            ->where('ref_id', (string) $approved->getKey())
            ->first();

        $this->assertNotNull($ledger);
    }
}
