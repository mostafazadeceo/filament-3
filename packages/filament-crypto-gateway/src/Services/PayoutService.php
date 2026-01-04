<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Services;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentCryptoCore\Services\CryptoAuditService;
use Haida\FilamentCryptoCore\Services\LedgerService;
use Haida\FilamentCryptoGateway\DTOs\PayoutCreateData;
use Haida\FilamentCryptoGateway\DTOs\ProviderPayoutData;
use Haida\FilamentCryptoGateway\Enums\CryptoPayoutStatus;
use Haida\FilamentCryptoGateway\Models\CryptoPayout;
use Haida\FilamentCryptoGateway\Models\CryptoPayoutDestination;
use Haida\FilamentCryptoGateway\Models\CryptoProviderAccount;
use Illuminate\Database\DatabaseManager;
use Illuminate\Validation\ValidationException;

class PayoutService
{
    public function __construct(
        protected DatabaseManager $db,
        protected ProviderRegistry $providers,
        protected LedgerService $ledger,
        protected FeePolicyService $fees,
        protected PlanService $planService,
        protected CryptoAuditService $audit,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public function create(array $payload): CryptoPayout
    {
        $tenantId = $payload['tenant_id'] ?? TenantContext::getTenantId();
        if (! $tenantId) {
            throw ValidationException::withMessages(['tenant_id' => 'شناسه تننت الزامی است.']);
        }

        if (! $this->planService->allowsFeature($tenantId, 'crypto.payouts')) {
            throw ValidationException::withMessages(['payouts' => 'برداشت در پلن فعلی فعال نیست.']);
        }

        $provider = (string) ($payload['provider'] ?? 'cryptomus');
        $orderId = (string) ($payload['order_id'] ?? '');
        if ($orderId === '') {
            throw ValidationException::withMessages(['order_id' => 'شناسه سفارش الزامی است.']);
        }

        $existing = CryptoPayout::query()
            ->where('tenant_id', $tenantId)
            ->where('provider', $provider)
            ->where('order_id', $orderId)
            ->first();

        if ($existing) {
            return $existing;
        }

        $data = new PayoutCreateData(
            (int) $tenantId,
            $provider,
            $orderId,
            (string) ($payload['to_address'] ?? ''),
            (string) ($payload['amount'] ?? 0),
            (string) ($payload['currency'] ?? config('filament-crypto-gateway.defaults.currency', 'USDT')),
            $payload['network'] ?? null,
            $payload['meta'] ?? [],
        );

        if ($data->toAddress === '') {
            throw ValidationException::withMessages(['to_address' => 'آدرس مقصد الزامی است.']);
        }

        $this->assertWhitelisted($data);

        $policyFee = $this->fees->calculatePayoutFee((float) $data->amount, (int) $tenantId);

        if ($this->requiresApproval((int) $tenantId)) {
            return $this->createPendingPayout($data, $policyFee);
        }

        if ((bool) config('filament-crypto-gateway.fake', false)) {
            return $this->createFakePayout($data, $policyFee);
        }

        $account = $this->resolveAccount($tenantId, $provider);
        $adapter = $this->providers->get($provider);
        $providerPayout = $adapter->createPayout($data, $account);

        $payout = $this->persistPayout($data, $providerPayout, $policyFee);

        if ($providerPayout->status === CryptoPayoutStatus::Completed) {
            $this->applyProviderUpdate($payout, $providerPayout, [
                'source' => 'create',
            ]);
        }

        return $payout;
    }

    public function approve(CryptoPayout $payout, ?int $approvedBy = null, ?string $note = null): CryptoPayout
    {
        if ($payout->status !== CryptoPayoutStatus::PendingApproval->value) {
            return $payout;
        }

        $payout->update([
            'approved_at' => now(),
            'approved_by' => $approvedBy,
            'approval_note' => $note,
            'meta' => array_merge($payout->meta ?? [], [
                'approval' => [
                    'status' => 'approved',
                    'note' => $note,
                    'at' => now()->toIso8601String(),
                ],
            ]),
        ]);

        return $this->submitApprovedPayout($payout->refresh());
    }

    public function reject(CryptoPayout $payout, ?int $approvedBy = null, ?string $note = null): CryptoPayout
    {
        if ($payout->is_final) {
            return $payout;
        }

        $payout->update([
            'status' => CryptoPayoutStatus::Cancelled->value,
            'is_final' => true,
            'approved_at' => now(),
            'approved_by' => $approvedBy,
            'approval_note' => $note,
            'fail_reason' => $payout->fail_reason ?? 'approval_rejected',
            'meta' => array_merge($payout->meta ?? [], [
                'approval' => [
                    'status' => 'rejected',
                    'note' => $note,
                    'at' => now()->toIso8601String(),
                ],
            ]),
        ]);

        $this->audit->record(
            'crypto.payout.approval',
            CryptoPayout::class,
            (string) $payout->getKey(),
            'رد برداشت رمزارز',
            [
                'status' => CryptoPayoutStatus::Cancelled->value,
                'provider' => $payout->provider,
            ]
        );

        return $payout->refresh();
    }

    /**
     * @param  array<string, mixed>  $context
     */
    public function applyProviderUpdate(CryptoPayout $payout, ProviderPayoutData $providerPayout, array $context = []): CryptoPayout
    {
        return $this->db->transaction(function () use ($payout, $providerPayout, $context) {
            $updates = [
                'status' => $providerPayout->status->value,
                'is_final' => $providerPayout->isFinal,
                'txid' => $providerPayout->txid ?? $payout->txid,
                'fail_reason' => $providerPayout->failReason ?? $payout->fail_reason,
                'fee' => $providerPayout->fee ?? $payout->fee,
            ];

            if (! $payout->external_uuid && $providerPayout->externalId !== '') {
                $updates['external_uuid'] = $providerPayout->externalId;
            }

            $payout->update([
                ...$updates,
                'meta' => array_merge($payout->meta ?? [], [
                    'provider_update' => $providerPayout->raw,
                    'context' => $context,
                ]),
            ]);

            $amount = (float) ($context['amount'] ?? $providerPayout->amount);
            $currency = (string) ($context['currency'] ?? $providerPayout->currency);

            if ($providerPayout->status === CryptoPayoutStatus::Completed) {
                $this->recordLedgerForPayout($payout, $amount, $currency);
            }

            $this->audit->record(
                'crypto.payout.status',
                CryptoPayout::class,
                (string) $payout->getKey(),
                'به‌روزرسانی وضعیت برداشت رمزارز',
                [
                    'status' => $providerPayout->status->value,
                    'provider' => $payout->provider,
                ]
            );

            return $payout->refresh();
        });
    }

    protected function resolveAccount(int $tenantId, string $provider): CryptoProviderAccount
    {
        $account = CryptoProviderAccount::query()
            ->withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->where('provider', $provider)
            ->where('is_active', true)
            ->first();

        if (! $account) {
            throw ValidationException::withMessages(['provider' => 'اتصال فعال برای این درگاه یافت نشد.']);
        }

        return $account;
    }

    protected function recordLedgerForPayout(CryptoPayout $payout, float $amount, string $currency): void
    {
        $defaults = (array) config('filament-crypto-core.ledger.default_accounts', []);
        $payable = $defaults['merchant_payable']['code'] ?? 'MERCHANT_PAYABLE';
        $wallet = $defaults['wallet']['code'] ?? 'CRYPTO_WALLET';
        $platformRevenue = $defaults['platform_revenue']['code'] ?? 'PLATFORM_REVENUE';

        $fee = (float) ($payout->fee ?? 0);

        $entries = [
            [
                'account_code' => $payable,
                'debit' => $amount + $fee,
                'credit' => 0,
                'currency' => $currency,
            ],
            [
                'account_code' => $wallet,
                'debit' => 0,
                'credit' => $amount,
                'currency' => $currency,
            ],
        ];

        if ($fee > 0) {
            $entries[] = [
                'account_code' => $platformRevenue,
                'debit' => 0,
                'credit' => $fee,
                'currency' => $currency,
            ];
        }

        $this->ledger->record(
            'crypto_payout',
            $payout->getKey(),
            $payout->tenant_id,
            'ثبت برداشت رمزارز',
            $entries,
            $payout->updated_at
        );
    }

    /**
     * @param  array<string, mixed>  $policyFee
     */
    protected function persistPayout(PayoutCreateData $data, ProviderPayoutData $providerPayout, array $policyFee): CryptoPayout
    {
        return $this->db->transaction(function () use ($data, $providerPayout, $policyFee) {
            $fee = $providerPayout->fee ?? ($policyFee['fee'] ?? 0);

            return CryptoPayout::query()->create([
                'tenant_id' => $data->tenantId,
                'provider' => $data->provider,
                'order_id' => $data->orderId,
                'external_uuid' => $providerPayout->externalId,
                'to_address' => $data->toAddress,
                'amount' => $providerPayout->amount,
                'currency' => $providerPayout->currency,
                'network' => $providerPayout->network,
                'fee' => $fee,
                'status' => $providerPayout->status->value,
                'is_final' => $providerPayout->isFinal,
                'fail_reason' => $providerPayout->failReason,
                'txid' => $providerPayout->txid,
                'meta' => array_merge($data->meta, [
                    'provider' => $providerPayout->raw,
                    'policy_fee' => $policyFee,
                ]),
            ]);
        });
    }

    /**
     * @param  array<string, mixed>  $policyFee
     */
    protected function createPendingPayout(PayoutCreateData $data, array $policyFee): CryptoPayout
    {
        return CryptoPayout::query()->create([
            'tenant_id' => $data->tenantId,
            'provider' => $data->provider,
            'order_id' => $data->orderId,
            'external_uuid' => null,
            'to_address' => $data->toAddress,
            'amount' => $data->amount,
            'currency' => $data->currency,
            'network' => $data->network,
            'fee' => $policyFee['fee'] ?? 0,
            'status' => CryptoPayoutStatus::PendingApproval->value,
            'is_final' => false,
            'meta' => array_merge($data->meta, [
                'policy_fee' => $policyFee,
                'request' => [
                    'requested_at' => now()->toIso8601String(),
                ],
            ]),
        ]);
    }

    protected function submitApprovedPayout(CryptoPayout $payout): CryptoPayout
    {
        $data = new PayoutCreateData(
            (int) $payout->tenant_id,
            (string) $payout->provider,
            (string) $payout->order_id,
            (string) $payout->to_address,
            (string) $payout->amount,
            (string) $payout->currency,
            $payout->network,
            $payout->meta ?? [],
        );

        $this->assertWhitelisted($data);
        $this->touchDestination($data);

        $account = $this->resolveAccount((int) $payout->tenant_id, (string) $payout->provider);
        $adapter = $this->providers->get((string) $payout->provider);
        $providerPayout = $adapter->createPayout($data, $account);

        return $this->applyProviderUpdate($payout->refresh(), $providerPayout, [
            'source' => 'approval',
        ]);
    }

    /**
     * @param  array<string, mixed>  $policyFee
     */
    protected function createFakePayout(PayoutCreateData $data, array $policyFee): CryptoPayout
    {
        $providerPayout = new ProviderPayoutData(
            $data->provider,
            'fake-'.uniqid(),
            $data->orderId,
            $data->amount,
            $data->currency,
            $data->network,
            $data->toAddress,
            CryptoPayoutStatus::Processing,
            false,
            null,
            null,
            ['fake' => true],
        );

        return $this->persistPayout($data, $providerPayout, $policyFee);
    }

    protected function requiresApproval(int $tenantId): bool
    {
        $requires = (bool) config('filament-crypto-gateway.payouts.require_approval', true);

        if (! $requires) {
            return false;
        }

        return $this->planService->allowsFeature($tenantId, 'crypto.payouts');
    }

    protected function assertWhitelisted(PayoutCreateData $data): void
    {
        if (! (bool) config('filament-crypto-gateway.payouts.whitelist.enabled', true)) {
            return;
        }

        $query = CryptoPayoutDestination::query()
            ->where('tenant_id', $data->tenantId)
            ->where('address', $data->toAddress)
            ->where('status', 'active');

        if ($data->currency !== '') {
            $query->where(function ($builder) use ($data) {
                $builder->whereNull('currency')
                    ->orWhere('currency', $data->currency);
            });
        }

        if ($data->network) {
            $query->where(function ($builder) use ($data) {
                $builder->whereNull('network')
                    ->orWhere('network', $data->network);
            });
        }

        if (! $query->exists()) {
            throw ValidationException::withMessages(['to_address' => 'آدرس مقصد در لیست سفید نیست.']);
        }
    }

    protected function touchDestination(PayoutCreateData $data): void
    {
        CryptoPayoutDestination::query()
            ->where('tenant_id', $data->tenantId)
            ->where('address', $data->toAddress)
            ->where('status', 'active')
            ->update(['last_used_at' => now()]);
    }
}
