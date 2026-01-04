<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Services;

use Haida\FilamentCryptoGateway\Enums\CryptoInvoiceStatus;
use Haida\FilamentCryptoGateway\Enums\CryptoWebhookCallStatus;
use Haida\FilamentCryptoGateway\Jobs\ProcessWebhookCall;
use Haida\FilamentCryptoGateway\Models\CryptoInvoice;
use Haida\FilamentCryptoGateway\Models\CryptoProviderAccount;
use Haida\FilamentCryptoGateway\Models\CryptoReconciliation;
use Haida\FilamentCryptoGateway\Models\CryptoWebhookCall;
use Illuminate\Support\Str;
use RuntimeException;

class ReconcileService
{
    public function __construct(
        protected ProviderRegistry $registry,
    ) {}

    public function run(int $tenantId, string $scope = 'invoices'): CryptoReconciliation
    {
        $reconcile = CryptoReconciliation::query()->create([
            'tenant_id' => $tenantId,
            'scope' => $scope,
            'started_at' => now(),
            'status' => 'running',
        ]);

        try {
            if ($scope === 'invoices') {
                $summary = $this->reconcileInvoices($tenantId);
                $reconcile->update([
                    'status' => 'completed',
                    'ended_at' => now(),
                    'result_json' => $summary,
                ]);
            } elseif (in_array($scope, ['daily', 'daily_balance'], true)) {
                $summary = $this->reconcileDaily($tenantId);
                $reconcile->update([
                    'status' => 'completed',
                    'ended_at' => now(),
                    'result_json' => $summary,
                ]);
            } else {
                $reconcile->update([
                    'status' => 'completed',
                    'ended_at' => now(),
                    'result_json' => ['message' => 'no-op'],
                ]);
            }
        } catch (RuntimeException $exception) {
            $reconcile->update([
                'status' => 'failed',
                'ended_at' => now(),
                'result_json' => ['error' => $exception->getMessage()],
            ]);
        }

        return $reconcile->refresh();
    }

    /**
     * @return array<string, mixed>
     */
    protected function reconcileInvoices(int $tenantId): array
    {
        $candidates = CryptoInvoice::query()
            ->where('tenant_id', $tenantId)
            ->whereIn('status', [
                CryptoInvoiceStatus::Unpaid,
                CryptoInvoiceStatus::Pending,
                CryptoInvoiceStatus::ConfirmCheck,
            ])
            ->get();

        $updated = 0;
        $errors = [];

        foreach ($candidates as $invoice) {
            $account = $this->resolveAccount($tenantId, $invoice->provider);
            $provider = $this->registry->get($invoice->provider);
            $lookup = (string) ($invoice->external_uuid ?: $invoice->order_id);
            $providerInvoice = $provider->getInvoice($lookup, $account);

            if (! $providerInvoice) {
                continue;
            }

            if ($providerInvoice->status === $invoice->status && $providerInvoice->isFinal === $invoice->is_final) {
                continue;
            }

            try {
                $call = $this->createSyntheticCall($invoice, $providerInvoice);
                ProcessWebhookCall::dispatch($call->getKey());
                $updated++;
            } catch (\Throwable $exception) {
                $errors[] = [
                    'invoice_id' => $invoice->getKey(),
                    'error' => $exception->getMessage(),
                ];
            }
        }

        return [
            'updated' => $updated,
            'total' => $candidates->count(),
            'errors' => $errors,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function reconcileDaily(int $tenantId): array
    {
        $from = now()->subDay();

        $invoiceCounts = CryptoInvoice::query()
            ->where('tenant_id', $tenantId)
            ->where('updated_at', '>=', $from)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $payoutCounts = \Haida\FilamentCryptoGateway\Models\CryptoPayout::query()
            ->where('tenant_id', $tenantId)
            ->where('updated_at', '>=', $from)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $webhookBacklog = \Haida\FilamentCryptoGateway\Models\CryptoWebhookCall::query()
            ->where('tenant_id', $tenantId)
            ->whereIn('status', [
                CryptoWebhookCallStatus::Received->value,
                CryptoWebhookCallStatus::Processing->value,
            ])
            ->count();

        return [
            'range' => [
                'from' => $from->toIso8601String(),
                'to' => now()->toIso8601String(),
            ],
            'invoice_counts' => $invoiceCounts,
            'payout_counts' => $payoutCounts,
            'webhook_backlog' => $webhookBacklog,
            'note' => 'daily_reconciliation_summary',
        ];
    }

    protected function resolveAccount(int $tenantId, string $provider): CryptoProviderAccount
    {
        $account = CryptoProviderAccount::query()
            ->where('tenant_id', $tenantId)
            ->where('provider', $provider)
            ->where('is_active', true)
            ->first();

        if (! $account) {
            throw new RuntimeException('No active provider account configured.');
        }

        return $account;
    }

    protected function createSyntheticCall(CryptoInvoice $invoice, \Haida\FilamentCryptoGateway\DTOs\ProviderInvoiceData $providerInvoice): CryptoWebhookCall
    {
        $lookupType = $invoice->external_uuid ? 'invoice_external' : 'invoice_order';
        $lookupValue = $invoice->external_uuid ?: $invoice->order_id;

        $event = [
            'provider' => $invoice->provider,
            'event_id' => 'synthetic-'.Str::uuid()->toString(),
            'event_type' => 'synthetic',
            'signature_ok' => true,
            'ip_ok' => true,
            'lookup_type' => $lookupType,
            'lookup_value' => $lookupValue,
            'invoice_status' => $providerInvoice->status->value,
            'txid' => $providerInvoice->raw['txid'] ?? null,
            'amount' => $providerInvoice->amount,
            'currency' => $providerInvoice->currency,
            'confirmations' => $providerInvoice->raw['confirmations'] ?? null,
            'is_final' => $providerInvoice->isFinal,
        ];

        $idempotencyKey = implode('|', array_filter([
            $invoice->provider,
            'synthetic',
            $lookupType,
            $lookupValue,
            $providerInvoice->status->value,
        ]));

        return CryptoWebhookCall::query()->firstOrCreate([
            'provider' => $invoice->provider,
            'tenant_id' => $invoice->tenant_id,
            'idempotency_key' => $idempotencyKey,
        ], [
            'event_id' => $event['event_id'],
            'signature_ok' => true,
            'ip_ok' => true,
            'payload_hash' => hash('sha256', json_encode($event, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: ''),
            'payload_json' => [
                'payload' => $providerInvoice->raw,
                'event' => $event,
            ],
            'received_at' => now(),
            'status' => CryptoWebhookCallStatus::Received,
        ]);
    }
}
