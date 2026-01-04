<?php

namespace Haida\FilamentPayments\Providers;

use Haida\FilamentPayments\Contracts\PaymentProviderInterface;
use Haida\FilamentPayments\DTO\PaymentIntentResponse;
use Haida\FilamentPayments\DTO\PaymentReconciliationResult;
use Haida\FilamentPayments\DTO\PaymentRefundResult;
use Haida\FilamentPayments\DTO\PaymentVerificationResult;
use Haida\FilamentPayments\DTO\PaymentWebhookResult;
use Haida\FilamentPayments\Models\PaymentIntent;
use Haida\FilamentPayments\Models\PaymentProviderConnection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class IranRestGatewayProvider implements PaymentProviderInterface
{
    public function key(): string
    {
        return 'iran_rest';
    }

    public function createIntent(PaymentIntent $intent, array $payload = []): PaymentIntentResponse
    {
        $config = config('filament-payments.providers.iran_rest', []);
        $fake = (bool) ($config['fake'] ?? true);

        $merchantId = $payload['merchant_id'] ?? Arr::get($config, 'merchant_id');
        $callbackUrl = $payload['callback_url'] ?? Arr::get($config, 'callback_url');

        if ($fake || ! $merchantId) {
            $reference = 'IRN-'.Str::upper(Str::random(8));
            $redirectUrl = Arr::get($config, 'redirect_url', 'https://sandbox.example/pay/'.$reference);

            return new PaymentIntentResponse($reference, $redirectUrl, 'pending', [
                'fake' => true,
            ]);
        }

        $endpoint = Arr::get($config, 'endpoint');
        if (! $endpoint) {
            $reference = 'IRN-'.Str::upper(Str::random(8));
            $redirectUrl = Arr::get($config, 'redirect_url', 'https://sandbox.example/pay/'.$reference);

            return new PaymentIntentResponse($reference, $redirectUrl, 'pending', [
                'fake' => true,
                'reason' => 'missing_endpoint',
            ]);
        }

        $response = Http::asJson()->post($endpoint, [
            'merchant_id' => $merchantId,
            'amount' => $intent->amount,
            'currency' => $intent->currency,
            'callback_url' => $callbackUrl,
            'description' => $payload['description'] ?? ('Payment '.$intent->getKey()),
        ]);

        if (! $response->ok()) {
            $reference = 'IRN-'.Str::upper(Str::random(8));

            return new PaymentIntentResponse($reference, null, 'failed', [
                'error' => $response->body(),
            ]);
        }

        $data = $response->json();
        $reference = (string) Arr::get($data, 'authority', Arr::get($data, 'reference', ''));
        $redirectUrl = Arr::get($data, 'redirect_url');

        return new PaymentIntentResponse($reference, $redirectUrl, 'pending', [
            'raw' => $data,
        ]);
    }

    public function redirectUrl(PaymentIntent $intent): ?string
    {
        return $intent->redirect_url;
    }

    public function verifyCallback(array $payload, PaymentProviderConnection $connection): PaymentVerificationResult
    {
        $status = $payload['status'] ?? 'pending';
        $verified = in_array($status, ['ok', 'paid', 'success'], true);

        return new PaymentVerificationResult(
            $verified,
            $verified ? 'confirmed' : 'failed',
            $payload['reference'] ?? $payload['authority'] ?? null,
            $payload
        );
    }

    public function handleWebhook(array $headers, array $payload, PaymentProviderConnection $connection): PaymentWebhookResult
    {
        $status = $payload['status'] ?? 'pending';
        $processed = in_array($status, ['ok', 'paid', 'success'], true);

        return new PaymentWebhookResult(
            $processed,
            $processed ? 'processed' : 'ignored',
            $payload['reference'] ?? $payload['authority'] ?? null,
            $payload['type'] ?? null,
            $payload
        );
    }

    public function refund(PaymentIntent $intent, float $amount, array $payload = []): PaymentRefundResult
    {
        return new PaymentRefundResult(true, 'pending', $payload['reference'] ?? null, [
            'amount' => $amount,
        ]);
    }

    public function reconcile(PaymentProviderConnection $connection, ?\DateTimeInterface $from = null, ?\DateTimeInterface $to = null): PaymentReconciliationResult
    {
        return new PaymentReconciliationResult(true, 'completed', [
            'from' => $from?->format(DATE_ATOM),
            'to' => $to?->format(DATE_ATOM),
        ], [
            'note' => 'stub_reconcile',
        ]);
    }
}
