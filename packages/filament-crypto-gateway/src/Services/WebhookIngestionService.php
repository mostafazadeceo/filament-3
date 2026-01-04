<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Services;

use Haida\FilamentCryptoGateway\DTOs\WebhookEventData;
use Haida\FilamentCryptoGateway\Enums\CryptoWebhookCallStatus;
use Haida\FilamentCryptoGateway\Jobs\ProcessWebhookCall;
use Haida\FilamentCryptoGateway\Models\CryptoProviderAccount;
use Haida\FilamentCryptoGateway\Models\CryptoWebhookCall;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Bus;

class WebhookIngestionService
{
    public function __construct(
        protected ProviderRegistry $registry,
    ) {}

    /**
     * @param  array<string, mixed>  $headers
     */
    public function ingest(string $providerKey, array $headers, string $rawPayload, string $ip, ?int $tenantId): CryptoWebhookCall
    {
        $adapter = $this->registry->get($providerKey);
        $payloadArray = $this->decodePayload($rawPayload);

        [$account, $resolvedTenantId] = $this->resolveContext($providerKey, $payloadArray, $tenantId);
        $event = $adapter->verifyAndParseWebhook($headers, $rawPayload, $ip, $account);

        $payloadHash = hash('sha256', $rawPayload);
        $idempotencyKey = $this->buildIdempotencyKey($event);

        $call = CryptoWebhookCall::query()->firstOrCreate([
            'provider' => $providerKey,
            'tenant_id' => $resolvedTenantId,
            'idempotency_key' => $idempotencyKey,
        ], [
            'event_id' => $event->eventId,
            'signature_ok' => $event->signatureOk,
            'ip_ok' => $event->ipOk,
            'payload_hash' => $payloadHash,
            'headers_json' => $headers,
            'remote_ip' => $ip,
            'raw_payload' => $rawPayload,
            'payload_json' => [
                'payload' => $payloadArray,
                'event' => $this->eventToArray($event),
            ],
            'received_at' => now(),
            'status' => CryptoWebhookCallStatus::Received,
        ]);

        Bus::dispatch(new ProcessWebhookCall($call->getKey()));

        return $call->refresh();
    }

    protected function resolveAccount(int $tenantId, string $provider): ?CryptoProviderAccount
    {
        return CryptoProviderAccount::query()
            ->withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->where('provider', $provider)
            ->where('is_active', true)
            ->first();
    }

    protected function decodePayload(string $rawPayload): array
    {
        $decoded = json_decode($rawPayload, true);

        if (is_array($decoded)) {
            return $decoded;
        }

        $form = [];
        parse_str($rawPayload, $form);

        return is_array($form) ? $form : [];
    }

    protected function buildIdempotencyKey(WebhookEventData $event): string
    {
        if ($event->eventId !== '') {
            return $event->provider.'::'.$event->eventId;
        }

        $parts = [
            $event->provider,
            $event->lookupType,
            $event->lookupValue,
            $event->invoiceStatus?->value ?? $event->payoutStatus?->value ?? 'unknown',
            $event->txid ?? '',
        ];

        return implode('|', array_filter($parts, fn ($value) => $value !== ''));
    }

    /**
     * @return array<string, mixed>
     */
    protected function eventToArray(WebhookEventData $event): array
    {
        return [
            'provider' => $event->provider,
            'event_id' => $event->eventId,
            'event_type' => $event->eventType,
            'signature_ok' => $event->signatureOk,
            'ip_ok' => $event->ipOk,
            'lookup_type' => $event->lookupType,
            'lookup_value' => $event->lookupValue,
            'invoice_status' => $event->invoiceStatus?->value,
            'payout_status' => $event->payoutStatus?->value,
            'txid' => $event->txid,
            'amount' => $event->amount,
            'currency' => $event->currency,
            'confirmations' => $event->confirmations,
            'is_final' => $event->isFinal,
            'payload' => Arr::except($event->payload, ['sign', 'signature', 'api_key', 'secret']),
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array{0: ?CryptoProviderAccount, 1: ?int}
     */
    protected function resolveContext(string $provider, array $payload, ?int $tenantId): array
    {
        $resolvedTenantId = $tenantId ?: $this->tenantFromPayload($payload);
        $account = $resolvedTenantId ? $this->resolveAccount($resolvedTenantId, $provider) : null;

        if ($account) {
            return [$account, $resolvedTenantId];
        }

        $merchantId = $payload['merchant'] ?? $payload['merchant_id'] ?? null;
        if (is_string($merchantId) && $merchantId !== '') {
            $account = CryptoProviderAccount::query()
                ->withoutGlobalScopes()
                ->where('provider', $provider)
                ->where('merchant_id', $merchantId)
                ->where('is_active', true)
                ->first();

            if ($account) {
                return [$account, (int) $account->tenant_id];
            }
        }

        $orderId = $this->payloadOrderId($payload);
        if ($orderId !== '') {
            $tenantFromOrder = $this->tenantFromOrder($provider, $orderId);
            if ($tenantFromOrder) {
                return [$this->resolveAccount($tenantFromOrder, $provider), $tenantFromOrder];
            }
        }

        $externalId = $this->payloadExternalId($payload);
        if ($externalId !== '') {
            $tenantFromExternal = $this->tenantFromExternal($provider, $externalId);
            if ($tenantFromExternal) {
                return [$this->resolveAccount($tenantFromExternal, $provider), $tenantFromExternal];
            }
        }

        return [null, $resolvedTenantId];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function tenantFromPayload(array $payload): ?int
    {
        $tenant = $payload['tenant_id'] ?? Arr::get($payload, 'metadata.tenant_id') ?? Arr::get($payload, 'metadata.tenantId');

        return $tenant ? (int) $tenant : null;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function payloadOrderId(array $payload): string
    {
        return (string) ($payload['order_id']
            ?? $payload['custom']
            ?? Arr::get($payload, 'metadata.orderId')
            ?? Arr::get($payload, 'metadata.order_id')
            ?? '');
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function payloadExternalId(array $payload): string
    {
        return (string) ($payload['uuid']
            ?? $payload['invoice_id']
            ?? $payload['txn_id']
            ?? Arr::get($payload, 'event.data.id')
            ?? Arr::get($payload, 'id')
            ?? '');
    }

    protected function tenantFromOrder(string $provider, string $orderId): ?int
    {
        $invoiceTenant = \Haida\FilamentCryptoGateway\Models\CryptoInvoice::query()
            ->withoutGlobalScopes()
            ->where('provider', $provider)
            ->where('order_id', $orderId)
            ->value('tenant_id');

        if ($invoiceTenant) {
            return (int) $invoiceTenant;
        }

        $payoutTenant = \Haida\FilamentCryptoGateway\Models\CryptoPayout::query()
            ->withoutGlobalScopes()
            ->where('provider', $provider)
            ->where('order_id', $orderId)
            ->value('tenant_id');

        return $payoutTenant ? (int) $payoutTenant : null;
    }

    protected function tenantFromExternal(string $provider, string $externalId): ?int
    {
        $invoiceTenant = \Haida\FilamentCryptoGateway\Models\CryptoInvoice::query()
            ->withoutGlobalScopes()
            ->where('provider', $provider)
            ->where('external_uuid', $externalId)
            ->value('tenant_id');

        if ($invoiceTenant) {
            return (int) $invoiceTenant;
        }

        $payoutTenant = \Haida\FilamentCryptoGateway\Models\CryptoPayout::query()
            ->withoutGlobalScopes()
            ->where('provider', $provider)
            ->where('external_uuid', $externalId)
            ->value('tenant_id');

        return $payoutTenant ? (int) $payoutTenant : null;
    }
}
