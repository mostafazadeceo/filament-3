<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Services;

use Haida\FilamentCryptoGateway\Enums\CryptoInvoicePaymentStatus;
use Haida\FilamentCryptoGateway\Enums\CryptoWebhookCallStatus;
use Haida\FilamentCryptoGateway\Models\CryptoInvoice;
use Haida\FilamentCryptoGateway\Models\CryptoInvoicePayment;
use Haida\FilamentCryptoGateway\Models\CryptoPayout;
use Haida\FilamentCryptoGateway\Models\CryptoWebhookCall;
use Illuminate\Support\Arr;
use Throwable;

class WebhookProcessor
{
    public function __construct(
        protected InvoiceService $invoiceService,
        protected PayoutService $payoutService,
    ) {}

    public function process(CryptoWebhookCall $call): void
    {
        if ($call->processed_at) {
            return;
        }

        if (! $call->signature_ok || ! $call->ip_ok) {
            $call->update([
                'status' => CryptoWebhookCallStatus::Rejected,
                'processed_at' => now(),
                'error' => 'signature_or_ip_failed',
            ]);

            return;
        }

        $event = Arr::get($call->payload_json ?? [], 'event', []);
        if (! is_array($event) || $event === []) {
            $this->markFailed($call, 'missing_event_payload');

            return;
        }

        if (! $call->tenant_id) {
            $this->markFailed($call, 'missing_tenant_context');

            return;
        }

        try {
            $call->update(['status' => CryptoWebhookCallStatus::Processing]);

            $this->handleEvent($call, $event);

            $call->update([
                'status' => CryptoWebhookCallStatus::Processed,
                'processed_at' => now(),
            ]);
        } catch (Throwable $exception) {
            $shouldThrow = $this->markFailed($call, $exception->getMessage());
            if ($shouldThrow) {
                throw $exception;
            }
        }
    }

    public function reprocess(CryptoWebhookCall $call): void
    {
        $call->update([
            'processed_at' => null,
            'status' => CryptoWebhookCallStatus::Received,
            'error' => null,
            'retry_count' => 0,
        ]);

        $this->process($call->refresh());
    }

    /**
     * @param  array<string, mixed>  $event
     */
    protected function handleEvent(CryptoWebhookCall $call, array $event): void
    {
        $lookupType = (string) ($event['lookup_type'] ?? '');
        $lookupValue = (string) ($event['lookup_value'] ?? '');

        if ($lookupType === '' || $lookupValue === '') {
            throw new \RuntimeException('Webhook event missing lookup info.');
        }

        if (str_starts_with($lookupType, 'invoice')) {
            $invoice = $this->resolveInvoice($call->provider, (int) $call->tenant_id, $lookupType, $lookupValue);
            if (! $invoice) {
                throw new \RuntimeException('Invoice not found for webhook.');
            }

            $invoiceStatus = $event['invoice_status'] ?? null;
            $this->syncInvoicePayment($invoice, $event, $call);

            if ($invoiceStatus) {
                $statusEnum = \Haida\FilamentCryptoGateway\Enums\CryptoInvoiceStatus::tryFrom($invoiceStatus)
                    ?? \Haida\FilamentCryptoGateway\Enums\CryptoInvoiceStatus::Pending;
                $this->invoiceService->applyProviderUpdate(
                    $invoice->refresh(),
                    new \Haida\FilamentCryptoGateway\DTOs\ProviderInvoiceData(
                        $call->provider,
                        (string) ($invoice->external_uuid ?? $lookupValue),
                        (string) $invoice->order_id,
                        (string) $invoice->amount,
                        (string) $invoice->currency,
                        $invoice->to_currency,
                        $invoice->network,
                        $invoice->address,
                        $statusEnum,
                        (bool) ($event['is_final'] ?? $invoice->is_final),
                        $invoice->expires_at?->toIso8601String(),
                        $invoice->meta ?? []
                    ),
                    [
                        'source' => 'webhook',
                        'amount' => $event['amount'] ?? null,
                        'currency' => $event['currency'] ?? null,
                    ]
                );
            }

            return;
        }

        if (str_starts_with($lookupType, 'payout')) {
            $payout = $this->resolvePayout($call->provider, (int) $call->tenant_id, $lookupType, $lookupValue);
            if (! $payout) {
                throw new \RuntimeException('Payout not found for webhook.');
            }

            $payoutStatus = $event['payout_status'] ?? null;
            if ($payoutStatus) {
                $statusEnum = \Haida\FilamentCryptoGateway\Enums\CryptoPayoutStatus::tryFrom($payoutStatus)
                    ?? \Haida\FilamentCryptoGateway\Enums\CryptoPayoutStatus::Processing;
                $this->payoutService->applyProviderUpdate(
                    $payout->refresh(),
                    new \Haida\FilamentCryptoGateway\DTOs\ProviderPayoutData(
                        $call->provider,
                        (string) ($payout->external_uuid ?? $lookupValue),
                        (string) $payout->order_id,
                        (string) $payout->amount,
                        (string) $payout->currency,
                        $payout->network,
                        $payout->to_address,
                        $statusEnum,
                        (bool) ($event['is_final'] ?? $payout->is_final),
                        $event['txid'] ?? $payout->txid,
                        $payout->fail_reason,
                        $payout->meta ?? []
                    ),
                    [
                        'source' => 'webhook',
                        'amount' => $event['amount'] ?? null,
                        'currency' => $event['currency'] ?? null,
                    ]
                );
            }
        }
    }

    /**
     * @param  array<string, mixed>  $event
     */
    protected function syncInvoicePayment(CryptoInvoice $invoice, array $event, CryptoWebhookCall $call): void
    {
        $txid = $event['txid'] ?? null;
        if (! $txid) {
            return;
        }

        $status = $this->resolvePaymentStatus($event);
        $payment = CryptoInvoicePayment::query()
            ->firstOrCreate([
                'invoice_id' => $invoice->getKey(),
                'tenant_id' => $invoice->tenant_id,
                'txid' => (string) $txid,
            ], [
                'status' => $status,
                'seen_at' => now(),
                'raw_payload_json' => $call->payload_json,
            ]);

        $payment->fill([
            'payer_amount' => $event['amount'] ?? $payment->payer_amount,
            'payer_currency' => $event['currency'] ?? $payment->payer_currency,
            'confirmations' => $event['confirmations'] ?? $payment->confirmations,
            'status' => $status,
            'seen_at' => $payment->seen_at ?? now(),
        ])->save();
    }

    /**
     * @param  array<string, mixed>  $event
     */
    protected function resolvePaymentStatus(array $event): string
    {
        $confirmations = (int) ($event['confirmations'] ?? 0);
        $invoiceStatus = (string) ($event['invoice_status'] ?? '');

        if (in_array($invoiceStatus, ['failed', 'cancelled'], true)) {
            return CryptoInvoicePaymentStatus::Failed->value;
        }

        if (in_array($invoiceStatus, ['paid', 'paid_over', 'completed'], true)) {
            return CryptoInvoicePaymentStatus::Confirmed->value;
        }

        if ($confirmations > 0) {
            return CryptoInvoicePaymentStatus::Confirming->value;
        }

        return CryptoInvoicePaymentStatus::Seen->value;
    }

    protected function markFailed(CryptoWebhookCall $call, string $error): bool
    {
        $maxRetries = (int) config('filament-crypto-gateway.webhooks.max_retries', 5);
        $retryCount = (int) $call->retry_count + 1;
        $shouldRetry = $retryCount < $maxRetries;

        $call->update([
            'status' => $shouldRetry ? CryptoWebhookCallStatus::Received : CryptoWebhookCallStatus::Failed,
            'processed_at' => $shouldRetry ? null : now(),
            'retry_count' => $retryCount,
            'error' => $error,
        ]);

        return $shouldRetry;
    }

    protected function resolveInvoice(string $provider, int $tenantId, string $lookupType, string $lookupValue): ?CryptoInvoice
    {
        $query = CryptoInvoice::query()
            ->where('provider', $provider)
            ->where('tenant_id', $tenantId);

        if ($lookupType === 'invoice_external') {
            return $query->where('external_uuid', $lookupValue)->first();
        }

        if ($lookupType === 'invoice_order') {
            return $query->where('order_id', $lookupValue)->first();
        }

        return $query->where('order_id', $lookupValue)
            ->orWhere('external_uuid', $lookupValue)
            ->first();
    }

    protected function resolvePayout(string $provider, int $tenantId, string $lookupType, string $lookupValue): ?CryptoPayout
    {
        $query = CryptoPayout::query()
            ->where('provider', $provider)
            ->where('tenant_id', $tenantId);

        if ($lookupType === 'payout_external') {
            return $query->where('external_uuid', $lookupValue)->first();
        }

        if ($lookupType === 'payout_order') {
            return $query->where('order_id', $lookupValue)->first();
        }

        return $query->where('order_id', $lookupValue)
            ->orWhere('external_uuid', $lookupValue)
            ->first();
    }
}
