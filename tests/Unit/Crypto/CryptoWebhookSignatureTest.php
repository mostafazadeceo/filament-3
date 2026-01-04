<?php

declare(strict_types=1);

namespace Tests\Unit\Crypto;

use Haida\FilamentCryptoGateway\Adapters\CoinPaymentsAdapter;
use Haida\FilamentCryptoGateway\Adapters\CoinbaseCommerceAdapter;
use Haida\FilamentCryptoGateway\Adapters\CryptomusAdapter;
use Haida\FilamentCryptoGateway\Models\CryptoProviderAccount;
use Tests\TestCase;

class CryptoWebhookSignatureTest extends TestCase
{
    public function test_cryptomus_signature_validation(): void
    {
        $apiKey = 'cryptomus-key';
        $payloadForSign = [
            'uuid' => 'inv-1',
            'order_id' => 'ORDER-1',
            'status' => 'paid',
            'amount' => '10',
            'currency' => 'USDT',
        ];
        $rawForSign = json_encode($payloadForSign, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '';
        $sign = md5(base64_encode($rawForSign).$apiKey);

        $payload = array_merge($payloadForSign, ['sign' => $sign]);
        $raw = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '';

        $account = new CryptoProviderAccount();
        $account->api_key_encrypted = $apiKey;

        $event = (new CryptomusAdapter())->verifyAndParseWebhook([], $raw, '91.227.144.54', $account);

        $this->assertTrue($event->signatureOk);
        $this->assertTrue($event->ipOk);
    }

    public function test_coinbase_signature_validation(): void
    {
        $secret = 'coinbase-secret';
        $payload = [
            'event' => [
                'id' => 'evt-1',
                'type' => 'charge:confirmed',
                'data' => [
                    'id' => 'charge-1',
                    'metadata' => ['order_id' => 'ORDER-2'],
                    'pricing' => [
                        'local' => [
                            'amount' => '20',
                            'currency' => 'USD',
                        ],
                    ],
                ],
            ],
        ];
        $raw = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '';
        $signature = hash_hmac('sha256', $raw, $secret);

        $account = new CryptoProviderAccount();
        $account->secret_encrypted = $secret;

        $event = (new CoinbaseCommerceAdapter())->verifyAndParseWebhook([
            'X-CC-Webhook-Signature' => $signature,
        ], $raw, '127.0.0.1', $account);

        $this->assertTrue($event->signatureOk);
    }

    public function test_coinpayments_signature_validation(): void
    {
        $secret = 'coinpayments-secret';
        $payload = [
            'ipn_id' => 'ipn-1',
            'status' => '2',
            'txn_id' => 'tx-1',
            'custom' => 'ORDER-3',
            'amount2' => '15',
            'currency2' => 'USDT',
        ];
        $raw = http_build_query($payload);
        $signature = hash_hmac('sha512', $raw, $secret);

        $account = new CryptoProviderAccount();
        $account->secret_encrypted = $secret;

        $event = (new CoinPaymentsAdapter())->verifyAndParseWebhook([
            'X-CoinPayments-Signature' => $signature,
        ], $raw, '104.20.60.246', $account);

        $this->assertTrue($event->signatureOk);
        $this->assertTrue($event->ipOk);
    }
}
