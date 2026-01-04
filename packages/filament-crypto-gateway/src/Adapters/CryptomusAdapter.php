<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Adapters;

use Haida\FilamentCryptoGateway\Contracts\ProviderAdapterInterface;
use Haida\FilamentCryptoGateway\DTOs\InvoiceCreateData;
use Haida\FilamentCryptoGateway\DTOs\PayoutCreateData;
use Haida\FilamentCryptoGateway\DTOs\ProviderInvoiceData;
use Haida\FilamentCryptoGateway\DTOs\ProviderPayoutData;
use Haida\FilamentCryptoGateway\DTOs\WebhookEventData;
use Haida\FilamentCryptoGateway\Enums\CryptoInvoiceStatus;
use Haida\FilamentCryptoGateway\Enums\CryptoPayoutStatus;
use Haida\FilamentCryptoGateway\Models\CryptoProviderAccount;
use Haida\FilamentCryptoGateway\Support\CryptoStatusMapper;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class CryptomusAdapter implements ProviderAdapterInterface
{
    public function key(): string
    {
        return 'cryptomus';
    }

    public function supports(): array
    {
        return [
            'partial' => true,
            'refresh' => true,
            'convert' => true,
            'recurring' => false,
            'payouts' => true,
        ];
    }

    public function createInvoice(InvoiceCreateData $data, CryptoProviderAccount $account): ProviderInvoiceData
    {
        $body = [
            'amount' => (string) $data->amount,
            'currency' => strtoupper($data->currency),
            'order_id' => $data->orderId,
            'network' => $data->network,
            'url_callback' => $data->callbackUrl,
            'is_payment_multiple' => $data->isPaymentMultiple ? 1 : 0,
            'lifetime' => $this->normalizeLifetime($data->lifetime),
            'to_currency' => $data->toCurrency,
            'subtract' => $data->subtractPercent,
            'accuracy_payment_percent' => $data->tolerancePercent,
        ];

        if (isset($data->meta['currencies'])) {
            $body['currencies'] = $data->meta['currencies'];
        }

        [$payload, $json] = $this->encodePayload($body);

        $response = Http::timeout($this->timeout())
            ->withHeaders($this->authHeaders($json, $account))
            ->withBody($json, 'application/json')
            ->post($this->baseUrl().'/payment');

        $dataResponse = $response->json() ?? [];
        $result = $dataResponse['result'] ?? $dataResponse;

        $status = (string) ($result['status'] ?? 'unpaid');
        $statusEnum = CryptoStatusMapper::invoiceStatus('cryptomus', $status);
        $isFinal = (bool) ($result['is_final'] ?? CryptoStatusMapper::isFinalInvoiceStatus($statusEnum));

        return new ProviderInvoiceData(
            $this->key(),
            (string) ($result['uuid'] ?? $result['invoice_uuid'] ?? ''),
            (string) ($result['order_id'] ?? $data->orderId),
            (string) ($result['amount'] ?? $data->amount),
            (string) ($result['currency'] ?? $data->currency),
            $result['to_currency'] ?? $data->toCurrency,
            $result['network'] ?? $data->network,
            $result['address'] ?? null,
            $statusEnum,
            $isFinal,
            $result['expired_at'] ?? $result['expires_at'] ?? null,
            $result,
        );
    }

    public function getInvoice(string $externalId, CryptoProviderAccount $account): ?ProviderInvoiceData
    {
        if ($externalId === '') {
            return null;
        }

        [$payload, $json] = $this->encodePayload(['uuid' => $externalId]);

        $response = Http::timeout($this->timeout())
            ->withHeaders($this->authHeaders($json, $account))
            ->withBody($json, 'application/json')
            ->post($this->baseUrl().'/payment/info');

        $dataResponse = $response->json() ?? [];
        $result = $dataResponse['result'] ?? [];
        if ($result === []) {
            return null;
        }

        $status = (string) ($result['status'] ?? 'unpaid');
        $statusEnum = CryptoStatusMapper::invoiceStatus('cryptomus', $status);
        $isFinal = (bool) ($result['is_final'] ?? CryptoStatusMapper::isFinalInvoiceStatus($statusEnum));

        return new ProviderInvoiceData(
            $this->key(),
            (string) ($result['uuid'] ?? $externalId),
            (string) ($result['order_id'] ?? ''),
            (string) ($result['amount'] ?? '0'),
            (string) ($result['currency'] ?? ''),
            $result['to_currency'] ?? null,
            $result['network'] ?? null,
            $result['address'] ?? null,
            $statusEnum,
            $isFinal,
            $result['expired_at'] ?? $result['expires_at'] ?? null,
            $result,
        );
    }

    public function createPayout(PayoutCreateData $data, CryptoProviderAccount $account): ProviderPayoutData
    {
        $body = [
            'amount' => (string) $data->amount,
            'currency' => strtoupper($data->currency),
            'address' => $data->toAddress,
            'network' => $data->network,
            'order_id' => $data->orderId,
        ];

        [$payload, $json] = $this->encodePayload($body);

        $response = Http::timeout($this->timeout())
            ->withHeaders($this->authHeaders($json, $account))
            ->withBody($json, 'application/json')
            ->post($this->baseUrl().'/payout');

        $dataResponse = $response->json() ?? [];
        $result = $dataResponse['result'] ?? $dataResponse;

        $status = (string) ($result['status'] ?? 'pending');
        $statusEnum = CryptoStatusMapper::payoutStatus('cryptomus', $status);
        $isFinal = (bool) ($result['is_final'] ?? CryptoStatusMapper::isFinalPayoutStatus($statusEnum));

        return new ProviderPayoutData(
            $this->key(),
            (string) ($result['uuid'] ?? $result['id'] ?? ''),
            (string) ($result['order_id'] ?? $data->orderId),
            (string) ($result['amount'] ?? $data->amount),
            (string) ($result['currency'] ?? $data->currency),
            $result['network'] ?? $data->network,
            $data->toAddress,
            $statusEnum,
            $isFinal,
            $result['txid'] ?? null,
            $result['fail_reason'] ?? null,
            $result,
        );
    }

    public function getPayout(string $externalId, CryptoProviderAccount $account): ?ProviderPayoutData
    {
        if ($externalId === '') {
            return null;
        }

        [$payload, $json] = $this->encodePayload(['uuid' => $externalId]);

        $response = Http::timeout($this->timeout())
            ->withHeaders($this->authHeaders($json, $account))
            ->withBody($json, 'application/json')
            ->post($this->baseUrl().'/payout/info');

        $dataResponse = $response->json() ?? [];
        $result = $dataResponse['result'] ?? [];
        if ($result === []) {
            return null;
        }

        $status = (string) ($result['status'] ?? 'pending');
        $statusEnum = CryptoStatusMapper::payoutStatus('cryptomus', $status);
        $isFinal = (bool) ($result['is_final'] ?? CryptoStatusMapper::isFinalPayoutStatus($statusEnum));

        return new ProviderPayoutData(
            $this->key(),
            (string) ($result['uuid'] ?? $externalId),
            (string) ($result['order_id'] ?? ''),
            (string) ($result['amount'] ?? '0'),
            (string) ($result['currency'] ?? ''),
            $result['network'] ?? null,
            $result['address'] ?? null,
            $statusEnum,
            $isFinal,
            $result['txid'] ?? null,
            $result['fail_reason'] ?? null,
            $result,
        );
    }

    public function verifyAndParseWebhook(array $headers, string $rawPayload, string $ip, ?CryptoProviderAccount $account = null): WebhookEventData
    {
        $payload = json_decode($rawPayload, true) ?: [];
        $apiKey = (string) ($account?->api_key_encrypted ?? '');
        $signature = (string) Arr::get($payload, 'sign', '');
        $payloadForSign = $payload;
        unset($payloadForSign['sign']);
        $rawForSign = json_encode($payloadForSign, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '';
        $computed = md5(base64_encode($rawForSign).$apiKey);
        $signatureOk = $signature !== '' && hash_equals($computed, $signature);

        $ipAllowlist = (array) config('filament-crypto-gateway.providers.cryptomus.ip_allowlist', []);
        $ipOk = $ipAllowlist === [] || in_array($ip, $ipAllowlist, true);

        $status = (string) Arr::get($payload, 'status', 'unpaid');
        $eventType = (string) Arr::get($payload, 'type', Arr::get($payload, 'event_type', 'invoice'));

        $isPayout = (bool) (Arr::get($payload, 'payout_id') || Arr::get($payload, 'transfer_id') || $eventType === 'payout');
        $orderId = (string) Arr::get($payload, 'order_id', '');
        $externalId = (string) (Arr::get($payload, 'payout_id')
            ?? Arr::get($payload, 'transfer_id')
            ?? Arr::get($payload, 'uuid')
            ?? Arr::get($payload, 'invoice_uuid')
            ?? '');
        $lookupType = $isPayout
            ? ($orderId !== '' ? 'payout_order' : 'payout_external')
            : ($orderId !== '' ? 'invoice_order' : 'invoice_external');
        $lookupValue = $orderId !== '' ? $orderId : $externalId;

        $invoiceStatus = $isPayout ? null : CryptoStatusMapper::invoiceStatus('cryptomus', $status);
        $payoutStatus = $isPayout ? CryptoStatusMapper::payoutStatus('cryptomus', $status) : null;

        $final = $isPayout
            ? CryptoStatusMapper::isFinalPayoutStatus($payoutStatus ?? CryptoPayoutStatus::Processing)
            : CryptoStatusMapper::isFinalInvoiceStatus($invoiceStatus ?? CryptoInvoiceStatus::Pending);

        return new WebhookEventData(
            $this->key(),
            (string) (Arr::get($payload, 'event_id')
                ?? Arr::get($payload, 'uuid')
                ?? Arr::get($payload, 'payout_id')
                ?? Arr::get($payload, 'transfer_id')
                ?? ''),
            $eventType,
            $signatureOk,
            $ipOk,
            $lookupType,
            $lookupValue,
            $invoiceStatus,
            $payoutStatus,
            Arr::get($payload, 'txid'),
            (string) (Arr::get($payload, 'amount') ?? Arr::get($payload, 'payment_amount') ?? ''),
            Arr::get($payload, 'currency') ?? Arr::get($payload, 'payment_currency'),
            Arr::get($payload, 'confirmations'),
            (bool) (Arr::get($payload, 'is_final') ?? $final),
            $payload,
        );
    }

    protected function baseUrl(): string
    {
        return rtrim((string) config('filament-crypto-gateway.providers.cryptomus.base_url', ''), '/');
    }

    protected function timeout(): int
    {
        return (int) config('filament-crypto-gateway.providers.cryptomus.timeout', 10);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array{0: array<string, mixed>, 1: string}
     */
    protected function encodePayload(array $payload): array
    {
        $filtered = array_filter($payload, static fn ($value) => $value !== null && $value !== '');
        $json = json_encode($filtered, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '{}';

        return [$filtered, $json];
    }

    /**
     * @return array<string, string>
     */
    protected function authHeaders(string $json, CryptoProviderAccount $account): array
    {
        $apiKey = (string) ($account->api_key_encrypted ?? '');
        $merchantId = (string) ($account->merchant_id ?? '');
        $signature = md5(base64_encode($json).$apiKey);

        return [
            'merchant' => $merchantId,
            'sign' => $signature,
            'Content-Type' => 'application/json',
        ];
    }

    protected function normalizeLifetime(?int $seconds): ?int
    {
        if (! $seconds || $seconds <= 0) {
            return null;
        }

        return max(1, (int) ceil($seconds / 60));
    }
}
