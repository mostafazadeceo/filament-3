<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Adapters;

use Haida\FilamentCryptoGateway\Contracts\ProviderAdapterInterface;
use Haida\FilamentCryptoGateway\DTOs\InvoiceCreateData;
use Haida\FilamentCryptoGateway\DTOs\PayoutCreateData;
use Haida\FilamentCryptoGateway\DTOs\ProviderInvoiceData;
use Haida\FilamentCryptoGateway\DTOs\ProviderPayoutData;
use Haida\FilamentCryptoGateway\DTOs\WebhookEventData;
use Haida\FilamentCryptoGateway\Models\CryptoProviderAccount;
use Haida\FilamentCryptoGateway\Support\CryptoStatusMapper;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class CoinPaymentsAdapter implements ProviderAdapterInterface
{
    public function key(): string
    {
        return 'coinpayments';
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
        $payload = [
            'cmd' => 'create_transaction',
            'amount' => (string) $data->amount,
            'currency1' => strtoupper($data->currency),
            'currency2' => strtoupper($data->toCurrency ?? $data->currency),
            'custom' => $data->orderId,
            'ipn_url' => $data->callbackUrl,
        ];

        $dataResponse = $this->request($payload, $account);
        $result = $dataResponse['result'] ?? [];

        $status = (string) ($result['status'] ?? '0');
        $statusEnum = CryptoStatusMapper::invoiceStatus('coinpayments', $status);
        $isFinal = CryptoStatusMapper::isFinalInvoiceStatus($statusEnum);

        return new ProviderInvoiceData(
            $this->key(),
            (string) ($result['txn_id'] ?? ''),
            (string) ($result['custom'] ?? $data->orderId),
            (string) ($result['amount'] ?? $data->amount),
            (string) ($result['currency1'] ?? $data->currency),
            (string) ($result['currency2'] ?? $data->toCurrency ?? $data->currency),
            $data->network,
            $result['address'] ?? null,
            $statusEnum,
            $isFinal,
            $result['timeout'] ?? null,
            $result,
        );
    }

    public function getInvoice(string $externalId, CryptoProviderAccount $account): ?ProviderInvoiceData
    {
        if ($externalId === '') {
            return null;
        }

        $payload = [
            'cmd' => 'get_tx_info',
            'txid' => $externalId,
        ];

        $dataResponse = $this->request($payload, $account);
        $result = $dataResponse['result'] ?? [];
        if ($result === []) {
            return null;
        }

        $status = (string) ($result['status'] ?? '0');
        $statusEnum = CryptoStatusMapper::invoiceStatus('coinpayments', $status);
        $isFinal = CryptoStatusMapper::isFinalInvoiceStatus($statusEnum);

        return new ProviderInvoiceData(
            $this->key(),
            (string) ($result['txn_id'] ?? $externalId),
            (string) ($result['custom'] ?? ''),
            (string) ($result['amount'] ?? '0'),
            (string) ($result['currency1'] ?? ''),
            (string) ($result['currency2'] ?? null),
            $result['network'] ?? null,
            $result['address'] ?? null,
            $statusEnum,
            $isFinal,
            $result['timeout'] ?? null,
            $result,
        );
    }

    public function createPayout(PayoutCreateData $data, CryptoProviderAccount $account): ProviderPayoutData
    {
        $payload = [
            'cmd' => 'create_withdrawal',
            'amount' => (string) $data->amount,
            'currency' => strtoupper($data->currency),
            'address' => $data->toAddress,
            'auto_confirm' => 1,
            'custom_id' => $data->orderId,
        ];

        $dataResponse = $this->request($payload, $account);
        $result = $dataResponse['result'] ?? [];

        $status = (string) ($result['status'] ?? '0');
        $statusEnum = CryptoStatusMapper::payoutStatus('coinpayments', $status);
        $isFinal = CryptoStatusMapper::isFinalPayoutStatus($statusEnum);

        return new ProviderPayoutData(
            $this->key(),
            (string) ($result['id'] ?? $data->orderId),
            (string) ($result['custom_id'] ?? $data->orderId),
            (string) ($result['amount'] ?? $data->amount),
            (string) ($result['currency'] ?? $data->currency),
            $data->network,
            $data->toAddress,
            $statusEnum,
            $isFinal,
            $result['txid'] ?? null,
            $result['status_text'] ?? null,
            $result,
        );
    }

    public function getPayout(string $externalId, CryptoProviderAccount $account): ?ProviderPayoutData
    {
        if ($externalId === '') {
            return null;
        }

        $payload = [
            'cmd' => 'get_withdrawal_info',
            'id' => $externalId,
        ];

        $dataResponse = $this->request($payload, $account);
        $result = $dataResponse['result'] ?? [];
        if ($result === []) {
            return null;
        }

        $status = (string) ($result['status'] ?? '0');
        $statusEnum = CryptoStatusMapper::payoutStatus('coinpayments', $status);
        $isFinal = CryptoStatusMapper::isFinalPayoutStatus($statusEnum);

        return new ProviderPayoutData(
            $this->key(),
            (string) ($result['id'] ?? $externalId),
            (string) ($result['custom_id'] ?? ''),
            (string) ($result['amount'] ?? '0'),
            (string) ($result['currency'] ?? ''),
            $result['network'] ?? null,
            $result['address'] ?? null,
            $statusEnum,
            $isFinal,
            $result['txid'] ?? null,
            $result['status_text'] ?? null,
            $result,
        );
    }

    public function verifyAndParseWebhook(array $headers, string $rawPayload, string $ip, ?CryptoProviderAccount $account = null): WebhookEventData
    {
        $payload = $this->decodePayload($rawPayload);
        $signatureHeader = $this->headerValue($headers, 'X-CoinPayments-Signature');
        if ($signatureHeader === '') {
            $signatureHeader = $this->headerValue($headers, 'HMAC');
        }

        $secret = (string) ($account?->secret_encrypted ?? '');
        $computed = hash_hmac('sha512', $rawPayload, $secret);
        $signatureOk = $signatureHeader !== '' && hash_equals($computed, $signatureHeader);

        $ipAllowlist = (array) config('filament-crypto-gateway.providers.coinpayments.ip_allowlist', []);
        $ipOk = $ipAllowlist === [] || in_array($ip, $ipAllowlist, true);

        $status = (string) Arr::get($payload, 'status', '0');
        $invoiceStatus = CryptoStatusMapper::invoiceStatus('coinpayments', $status);
        $isFinal = CryptoStatusMapper::isFinalInvoiceStatus($invoiceStatus);

        $orderId = (string) Arr::get($payload, 'custom', '');
        $txnId = (string) Arr::get($payload, 'txn_id', '');

        return new WebhookEventData(
            $this->key(),
            (string) (Arr::get($payload, 'ipn_id') ?? $txnId),
            'invoice',
            $signatureOk,
            $ipOk,
            $orderId !== '' ? 'invoice_order' : 'invoice_external',
            $orderId !== '' ? $orderId : $txnId,
            $invoiceStatus,
            null,
            $txnId !== '' ? $txnId : null,
            (string) Arr::get($payload, 'amount2', ''),
            (string) Arr::get($payload, 'currency2', ''),
            Arr::get($payload, 'confirms'),
            (bool) Arr::get($payload, 'is_final', $isFinal),
            $payload,
        );
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    protected function request(array $payload, CryptoProviderAccount $account): array
    {
        $baseUrl = rtrim((string) config('filament-crypto-gateway.providers.coinpayments.base_url', ''), '/');
        $timeout = (int) config('filament-crypto-gateway.providers.coinpayments.timeout', 10);

        $body = array_merge($payload, [
            'key' => (string) ($account->api_key_encrypted ?? ''),
            'version' => 1,
        ]);

        $query = http_build_query($body);
        $signature = hash_hmac('sha512', $query, (string) ($account->secret_encrypted ?? ''));

        $response = Http::timeout($timeout)
            ->withHeaders([
                'HMAC' => $signature,
            ])
            ->asForm()
            ->post($baseUrl, $body);

        $data = $response->json();

        return is_array($data) ? $data : [];
    }

    /**
     * @return array<string, mixed>
     */
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
