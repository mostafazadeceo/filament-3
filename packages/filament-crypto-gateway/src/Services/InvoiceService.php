<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Services;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentCryptoCore\Services\CryptoAuditService;
use Haida\FilamentCryptoCore\Services\LedgerService;
use Haida\FilamentCryptoGateway\DTOs\InvoiceCreateData;
use Haida\FilamentCryptoGateway\DTOs\ProviderInvoiceData;
use Haida\FilamentCryptoGateway\Enums\CryptoInvoiceStatus;
use Haida\FilamentCryptoGateway\Models\CryptoInvoice;
use Haida\FilamentCryptoGateway\Models\CryptoProviderAccount;
use Haida\FilamentNotify\Core\Support\Triggers\TriggerDispatcher;
use Illuminate\Database\DatabaseManager;
use Illuminate\Validation\ValidationException;

class InvoiceService
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
    public function create(array $payload): CryptoInvoice
    {
        $tenantId = $payload['tenant_id'] ?? TenantContext::getTenantId();
        if (! $tenantId) {
            throw ValidationException::withMessages(['tenant_id' => 'شناسه تننت الزامی است.']);
        }

        if (! $this->planService->allowsFeature($tenantId, 'crypto.providers')) {
            throw ValidationException::withMessages(['plan' => 'درگاه رمزارز در پلن فعلی فعال نیست.']);
        }

        $provider = (string) ($payload['provider'] ?? 'cryptomus');
        $orderId = (string) ($payload['order_id'] ?? '');
        if ($orderId === '') {
            throw ValidationException::withMessages(['order_id' => 'شناسه سفارش الزامی است.']);
        }

        $existing = CryptoInvoice::query()
            ->where('tenant_id', $tenantId)
            ->where('provider', $provider)
            ->where('order_id', $orderId)
            ->first();

        if ($existing) {
            return $existing;
        }

        $account = $this->resolveAccount($tenantId, $provider);

        $tolerance = $payload['tolerance_percent'] ?? $payload['accuracy_payment_percent'] ?? null;
        $subtract = $payload['subtract_percent'] ?? $payload['subtract'] ?? null;
        $lifetime = $payload['lifetime'] ?? config('filament-crypto-gateway.defaults.invoice_lifetime', 1800);

        $data = new InvoiceCreateData(
            (int) $tenantId,
            $provider,
            $orderId,
            (string) ($payload['amount'] ?? 0),
            (string) ($payload['currency'] ?? config('filament-crypto-gateway.defaults.currency', 'USDT')),
            $payload['to_currency'] ?? null,
            $payload['network'] ?? null,
            is_null($lifetime) ? null : (int) $lifetime,
            (bool) ($payload['is_payment_multiple'] ?? false),
            $payload['callback_url'] ?? null,
            isset($tolerance) ? (float) $tolerance : null,
            isset($subtract) ? (float) $subtract : null,
            $payload['meta'] ?? [],
        );

        if ((bool) config('filament-crypto-gateway.fake', false)) {
            return $this->createFakeInvoice($data, $payload);
        }

        $adapter = $this->providers->get($provider);
        $providerInvoice = $adapter->createInvoice($data, $account);

        $invoice = $this->persistInvoice($data, $providerInvoice, $payload);

        if (in_array($providerInvoice->status, [
            CryptoInvoiceStatus::Paid,
            CryptoInvoiceStatus::PaidOver,
            CryptoInvoiceStatus::WrongAmount,
            CryptoInvoiceStatus::Completed,
        ], true)) {
            $this->applyProviderUpdate($invoice, $providerInvoice, [
                'source' => 'create',
            ]);
        }

        return $invoice;
    }

    public function refresh(CryptoInvoice $invoice): CryptoInvoice
    {
        $account = $this->resolveAccount($invoice->tenant_id, $invoice->provider);
        $adapter = $this->providers->get($invoice->provider);
        $providerInvoice = $adapter->getInvoice((string) ($invoice->external_uuid ?? $invoice->order_id), $account);

        if (! $providerInvoice) {
            return $invoice;
        }

        return $this->applyProviderUpdate($invoice, $providerInvoice, [
            'source' => 'refresh',
        ]);
    }

    /**
     * @param  array<string, mixed>  $context
     */
    public function applyProviderUpdate(CryptoInvoice $invoice, ProviderInvoiceData $providerInvoice, array $context = []): CryptoInvoice
    {
        return $this->db->transaction(function () use ($invoice, $providerInvoice, $context) {
            $updates = [
                'status' => $providerInvoice->status->value,
                'is_final' => $providerInvoice->isFinal,
                'address' => $providerInvoice->address ?? $invoice->address,
                'expires_at' => $providerInvoice->expiresAt ?? $invoice->expires_at,
            ];

            if (! $invoice->external_uuid && $providerInvoice->externalId !== '') {
                $updates['external_uuid'] = $providerInvoice->externalId;
            }

            $invoice->update([
                ...$updates,
                'meta' => array_merge($invoice->meta ?? [], [
                    'provider_update' => $providerInvoice->raw,
                    'context' => $context,
                ]),
            ]);

            $amount = (float) ($context['amount'] ?? $providerInvoice->amount);
            $currency = (string) ($context['currency'] ?? $providerInvoice->currency);

            if (in_array($providerInvoice->status, [
                CryptoInvoiceStatus::Paid,
                CryptoInvoiceStatus::PaidOver,
                CryptoInvoiceStatus::WrongAmount,
                CryptoInvoiceStatus::Completed,
            ], true)) {
                $this->recordLedgerForInvoice($invoice, $amount, $currency);
                $this->dispatchInvoicePaidNotification($invoice);
            }

            $this->audit->record(
                'crypto.invoice.status',
                CryptoInvoice::class,
                (string) $invoice->getKey(),
                'به‌روزرسانی وضعیت فاکتور رمزارز',
                [
                    'status' => $providerInvoice->status->value,
                    'provider' => $invoice->provider,
                ]
            );

            return $invoice->refresh();
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

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function persistInvoice(InvoiceCreateData $data, ProviderInvoiceData $providerInvoice, array $payload): CryptoInvoice
    {
        return $this->db->transaction(function () use ($data, $providerInvoice, $payload) {
            return CryptoInvoice::query()->create([
                'tenant_id' => $data->tenantId,
                'provider' => $data->provider,
                'order_id' => $data->orderId,
                'external_uuid' => $providerInvoice->externalId,
                'amount' => $providerInvoice->amount,
                'currency' => $providerInvoice->currency,
                'to_currency' => $providerInvoice->toCurrency,
                'network' => $providerInvoice->network,
                'address' => $providerInvoice->address,
                'status' => $providerInvoice->status->value,
                'is_final' => $providerInvoice->isFinal,
                'expires_at' => $payload['expires_at'] ?? $providerInvoice->expiresAt,
                'tolerance_percent' => $data->tolerancePercent ?? 0,
                'subtract_percent' => $data->subtractPercent ?? 0,
                'meta' => array_merge($data->meta, ['provider' => $providerInvoice->raw]),
            ]);
        });
    }

    protected function recordLedgerForInvoice(CryptoInvoice $invoice, float $amount, string $currency): void
    {
        $fees = $this->fees->calculateInvoiceFee($amount, $invoice->tenant_id);
        $feeAmount = (float) ($fees['fee'] ?? 0);

        $defaults = (array) config('filament-crypto-core.ledger.default_accounts', []);
        $clearing = $defaults['clearing']['code'] ?? 'CRYPTO_CLEARING';
        $payable = $defaults['merchant_payable']['code'] ?? 'MERCHANT_PAYABLE';
        $platformRevenue = $defaults['platform_revenue']['code'] ?? 'PLATFORM_REVENUE';
        $feeExpense = $defaults['fee_expense']['code'] ?? 'FEE_EXPENSE';

        $entries = [
            [
                'account_code' => $clearing,
                'debit' => $amount,
                'credit' => 0,
                'currency' => $currency,
            ],
            [
                'account_code' => $payable,
                'debit' => 0,
                'credit' => $amount,
                'currency' => $currency,
            ],
        ];

        if ($feeAmount > 0) {
            $entries[] = [
                'account_code' => $feeExpense,
                'debit' => $feeAmount,
                'credit' => 0,
                'currency' => $currency,
            ];
            $entries[] = [
                'account_code' => $platformRevenue,
                'debit' => 0,
                'credit' => $feeAmount,
                'currency' => $currency,
            ];
        }

        $this->ledger->record(
            'crypto_invoice',
            $invoice->getKey(),
            $invoice->tenant_id,
            'ثبت فاکتور رمزارز',
            $entries,
            $invoice->updated_at
        );
    }

    protected function dispatchInvoicePaidNotification(CryptoInvoice $invoice): void
    {
        $panelId = (string) config('filament-crypto-gateway.notifications.panel', 'tenant');
        $event = (string) config('filament-crypto-gateway.notifications.invoice_paid_event', 'crypto.invoice.paid');

        if ($panelId === '' || $event === '' || ! class_exists(TriggerDispatcher::class)) {
            return;
        }

        try {
            app(TriggerDispatcher::class)->dispatchForEloquent($panelId, $invoice, $event);
        } catch (\Throwable) {
            // Keep webhook processing resilient.
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function createFakeInvoice(InvoiceCreateData $data, array $payload): CryptoInvoice
    {
        $providerInvoice = new ProviderInvoiceData(
            $data->provider,
            'fake-'.uniqid(),
            $data->orderId,
            $data->amount,
            $data->currency,
            $data->toCurrency,
            $data->network,
            'fake-address',
            CryptoInvoiceStatus::Pending,
            false,
            now()->addSeconds((int) ($data->lifetime ?? 1800))->toIso8601String(),
            ['fake' => true],
        );

        return $this->persistInvoice($data, $providerInvoice, $payload);
    }
}
