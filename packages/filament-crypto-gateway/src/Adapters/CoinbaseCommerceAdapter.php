<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Adapters;

use Haida\FilamentCryptoGateway\Contracts\ProviderAdapterInterface;
use Haida\FilamentCryptoGateway\DTOs\InvoiceCreateData;
use Haida\FilamentCryptoGateway\DTOs\PayoutCreateData;
use Haida\FilamentCryptoGateway\DTOs\ProviderInvoiceData;
use Haida\FilamentCryptoGateway\DTOs\ProviderPayoutData;
use Haida\FilamentCryptoGateway\DTOs\WebhookEventData;
use Haida\FilamentCryptoGateway\Enums\CryptoPayoutStatus;
use Haida\FilamentCryptoGateway\Models\CryptoProviderAccount;
use Haida\FilamentCryptoGateway\Support\CryptoStatusMapper;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class CoinbaseCommerceAdapter implements ProviderAdapterInterface
{
    public function key(): string
    {
        return 'coinbase';
    }

    public function supports(): array
    {
        return [
            'partial' => false,
            'refresh' => true,
            'convert' => false,
            'recurring' => false,
            'payouts' => false,
        ];
    }

    public function createInvoice(InvoiceCreateData $data, CryptoProviderAccount $account): ProviderInvoiceData
    {
        $payload = [
            'name' => 'Order '.$data->orderId,
            'pricing_type' => 'fixed_price',
            'local_price' => [
                'amount' => (string) $data->amount,
                'currency' => strtoupper($data->currency),
            ],
            'metadata' => [
                'order_id' => $data->orderId,
                'tenant_id' => $data->tenantId,
            ],
        ];

        $response = Http::timeout($this->timeout())
            ->withHeaders($this->authHeaders($account))
            ->post($this->baseUrl().'/charges', $payload);

        $result = $response->json('data') ?? [];
        $status = (string) Arr::get($result, 'timeline.0.status', 'created');
        $statusEnum = CryptoStatusMapper::invoiceStatus('coinbase', 'charge:'.$status);

        return new ProviderInvoiceData(
            $this->key(),
            (string) ($result['id'] ?? ''),
            (string) Arr::get($result, 'metadata.order_id', $data->orderId),
            (string) Arr::get($result, 'pricing.local.amount', $data->amount),
            (string) Arr::get($result, 'pricing.local.currency', $data->currency),
            null,
            null,
            $result['hosted_url'] ?? null,
            $statusEnum,
            CryptoStatusMapper::isFinalInvoiceStatus($statusEnum),
            $result['expires_at'] ?? null,
            $result,
        );
    }

    public function getInvoice(string $externalId, CryptoProviderAccount $account): ?ProviderInvoiceData
    {
        if ($externalId === '') {
            return null;
        }

        $response = Http::timeout($this->timeout())
            ->withHeaders($this->authHeaders($account))
            ->get($this->baseUrl().'/charges/'.$externalId);

        $result = $response->json('data') ?? [];
        if ($result === []) {
            return null;
        }

        $status = (string) Arr::get($result, 'timeline.0.status', 'created');
        $statusEnum = CryptoStatusMapper::invoiceStatus('coinbase', 'charge:'.$status);

        return new ProviderInvoiceData(
            $this->key(),
            (string) ($result['id'] ?? $externalId),
            (string) Arr::get($result, 'metadata.order_id', ''),
            (string) Arr::get($result, 'pricing.local.amount', '0'),
            (string) Arr::get($result, 'pricing.local.currency', ''),
            null,
            null,
            $result['hosted_url'] ?? null,
            $statusEnum,
            CryptoStatusMapper::isFinalInvoiceStatus($statusEnum),
            $result['expires_at'] ?? null,
            $result,
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
            'coinbase_payout_unsupported',
            [],
        );
    }

    public function getPayout(string $externalId, CryptoProviderAccount $account): ?ProviderPayoutData
    {
        return null;
    }

    public function verifyAndParseWebhook(array $headers, string $rawPayload, string $ip, ?CryptoProviderAccount $account = null): WebhookEventData
    {
        $payload = json_decode($rawPayload, true) ?: [];
        $signatureHeader = $this->headerValue($headers, 'X-CC-Webhook-Signature');
        $secret = (string) ($account?->secret_encrypted ?? '');
        $computed = hash_hmac('sha256', $rawPayload, $secret);
        $signatureOk = $signatureHeader !== '' && hash_equals($computed, $signatureHeader);

        $eventType = (string) Arr::get($payload, 'event.type', 'charge:created');
        $statusEnum = CryptoStatusMapper::invoiceStatus('coinbase', $eventType);
        $dataPayload = Arr::get($payload, 'event.data', []);
        $orderId = (string) Arr::get($dataPayload, 'metadata.order_id', '');

        return new WebhookEventData(
            $this->key(),
            (string) Arr::get($payload, 'event.id', ''),
            $eventType,
            $signatureOk,
            true,
            $orderId !== '' ? 'invoice_order' : 'invoice_external',
            $orderId !== '' ? $orderId : (string) Arr::get($dataPayload, 'id', ''),
            $statusEnum,
            null,
            null,
            (string) Arr::get($dataPayload, 'pricing.local.amount', ''),
            (string) Arr::get($dataPayload, 'pricing.local.currency', ''),
            null,
            CryptoStatusMapper::isFinalInvoiceStatus($statusEnum),
            $payload,
        );
    }

    protected function baseUrl(): string
    {
        return rtrim((string) config('filament-crypto-gateway.providers.coinbase.base_url', ''), '/');
    }

    protected function timeout(): int
    {
        return (int) config('filament-crypto-gateway.providers.coinbase.timeout', 10);
    }

    /**
     * @return array<string, string>
     */
    protected function authHeaders(CryptoProviderAccount $account): array
    {
        return [
            'X-CC-Api-Key' => (string) ($account->api_key_encrypted ?? ''),
            'X-CC-Version' => '2018-03-22',
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * @param  array<string, mixed>  $headers
     */
    protected function headerValue(array $headers, string $key): string
    {
        foreach ($headers as $header => $value) {
            if (strcasecmp((string) $header, $key) === 0) {
                if (is_array($value)) {
                    return (string) ($value[0] ?? '');
                }

                return (string) $value;
            }
        }

        return '';
    }
}
