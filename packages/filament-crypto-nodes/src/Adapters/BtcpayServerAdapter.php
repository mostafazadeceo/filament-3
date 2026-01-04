<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoNodes\Adapters;

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

class BtcpayServerAdapter implements ProviderAdapterInterface
{
    public function key(): string
    {
        return 'btcpay';
    }

    public function supports(): array
    {
        return [
            'partial' => true,
            'refresh' => true,
            'convert' => false,
            'recurring' => false,
            'payouts' => false,
        ];
    }

    public function createInvoice(InvoiceCreateData $data, CryptoProviderAccount $account): ProviderInvoiceData
    {
        $payload = [
            'amount' => (float) $data->amount,
            'currency' => strtoupper($data->currency),
            'metadata' => [
                'orderId' => $data->orderId,
                'tenantId' => $data->tenantId,
            ],
        ];

        if ($data->callbackUrl) {
            $payload['notificationURL'] = $data->callbackUrl;
        }

        $response = Http::timeout($this->timeout())
            ->withToken($this->apiKey($account))
            ->post($this->baseUrl($account)."/api/v1/stores/{$this->storeId($account)}/invoices", $payload);

        $dataResponse = $response->json() ?? [];
        $status = (string) ($dataResponse['status'] ?? 'new');
        $additional = isset($dataResponse['additionalStatus']) ? (string) $dataResponse['additionalStatus'] : null;
        $statusEnum = CryptoStatusMapper::invoiceStatus('btcpay', $status, $additional);

        return new ProviderInvoiceData(
            $this->key(),
            (string) ($dataResponse['id'] ?? ''),
            (string) ($dataResponse['metadata']['orderId'] ?? $data->orderId),
            (string) ($dataResponse['amount'] ?? $data->amount),
            (string) ($dataResponse['currency'] ?? $data->currency),
            $data->toCurrency,
            $data->network,
            $dataResponse['checkoutLink'] ?? null,
            $statusEnum,
            CryptoStatusMapper::isFinalInvoiceStatus($statusEnum),
            $dataResponse['expirationTime'] ?? null,
            $dataResponse,
        );
    }

    public function getInvoice(string $externalId, CryptoProviderAccount $account): ?ProviderInvoiceData
    {
        if ($externalId === '') {
            return null;
        }

        $response = Http::timeout($this->timeout())
            ->withToken($this->apiKey($account))
            ->get($this->baseUrl($account)."/api/v1/stores/{$this->storeId($account)}/invoices/{$externalId}");

        $dataResponse = $response->json() ?? [];
        if ($dataResponse === []) {
            return null;
        }

        $status = (string) ($dataResponse['status'] ?? 'new');
        $additional = isset($dataResponse['additionalStatus']) ? (string) $dataResponse['additionalStatus'] : null;
        $statusEnum = CryptoStatusMapper::invoiceStatus('btcpay', $status, $additional);

        return new ProviderInvoiceData(
            $this->key(),
            (string) ($dataResponse['id'] ?? $externalId),
            (string) Arr::get($dataResponse, 'metadata.orderId', ''),
            (string) ($dataResponse['amount'] ?? '0'),
            (string) ($dataResponse['currency'] ?? ''),
            null,
            null,
            $dataResponse['checkoutLink'] ?? null,
            $statusEnum,
            CryptoStatusMapper::isFinalInvoiceStatus($statusEnum),
            $dataResponse['expirationTime'] ?? null,
            $dataResponse,
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
            'btcpay_payout_unsupported',
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
        $signatureHeader = $this->headerValue($headers, 'BTCPay-Sig');
        if ($signatureHeader === '') {
            $signatureHeader = $this->headerValue($headers, 'X-BTCPay-Signature');
        }

        $signature = str_starts_with($signatureHeader, 'sha256=')
            ? substr($signatureHeader, 7)
            : $signatureHeader;
        $secret = $this->webhookSecret($account);
        $expected = hash_hmac('sha256', $rawPayload, $secret);
        $signatureOk = $signature !== '' && hash_equals($expected, $signature);

        $status = (string) Arr::get($payload, 'status', 'new');
        $additional = Arr::get($payload, 'additionalStatus');
        $statusEnum = CryptoStatusMapper::invoiceStatus('btcpay', $status, is_string($additional) ? $additional : null);

        $orderId = (string) Arr::get($payload, 'metadata.orderId', '');
        $invoiceId = (string) (Arr::get($payload, 'invoiceId') ?: Arr::get($payload, 'id'));

        return new WebhookEventData(
            $this->key(),
            (string) ($payload['id'] ?? ''),
            'invoice',
            $signatureOk,
            true,
            $orderId !== '' ? 'invoice_order' : 'invoice_external',
            $orderId !== '' ? $orderId : $invoiceId,
            $statusEnum,
            null,
            null,
            (string) Arr::get($payload, 'amount', ''),
            (string) Arr::get($payload, 'currency', ''),
            Arr::get($payload, 'confirmations'),
            CryptoStatusMapper::isFinalInvoiceStatus($statusEnum),
            $payload,
        );
    }

    protected function baseUrl(CryptoProviderAccount $account): string
    {
        $config = (array) ($account->config_json ?? []);

        return rtrim((string) ($config['base_url'] ?? config('filament-crypto-nodes.btcpay.base_url', '')), '/');
    }

    protected function apiKey(CryptoProviderAccount $account): string
    {
        $config = (array) ($account->config_json ?? []);

        return (string) ($config['api_key'] ?? $account->api_key_encrypted ?? config('filament-crypto-nodes.btcpay.api_key', ''));
    }

    protected function storeId(CryptoProviderAccount $account): string
    {
        $config = (array) ($account->config_json ?? []);

        return (string) ($config['store_id'] ?? config('filament-crypto-nodes.btcpay.store_id', ''));
    }

    protected function webhookSecret(?CryptoProviderAccount $account): string
    {
        if ($account) {
            $config = (array) ($account->config_json ?? []);
            if (isset($config['webhook_secret'])) {
                return (string) $config['webhook_secret'];
            }
        }

        return (string) config('filament-crypto-nodes.btcpay.webhook_secret', '');
    }

    protected function timeout(): int
    {
        return (int) config('filament-crypto-nodes.btcpay.timeout', 10);
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
