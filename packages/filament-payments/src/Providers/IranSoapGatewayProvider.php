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
use Illuminate\Support\Str;

class IranSoapGatewayProvider implements PaymentProviderInterface
{
    public function key(): string
    {
        return 'iran_soap';
    }

    public function createIntent(PaymentIntent $intent, array $payload = []): PaymentIntentResponse
    {
        $config = config('filament-payments.providers.iran_soap', []);
        $reference = 'SOAP-'.Str::upper(Str::random(8));
        $redirectUrl = Arr::get($config, 'redirect_url');

        return new PaymentIntentResponse($reference, $redirectUrl, 'pending', [
            'stub' => true,
            'wsdl' => Arr::get($config, 'wsdl'),
        ]);
    }

    public function redirectUrl(PaymentIntent $intent): ?string
    {
        return $intent->redirect_url;
    }

    public function verifyCallback(array $payload, PaymentProviderConnection $connection): PaymentVerificationResult
    {
        $status = $payload['status'] ?? 'pending';
        $verified = in_array($status, ['OK', 'SUCCESS', '0', 'paid'], true);

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
        $processed = in_array($status, ['OK', 'SUCCESS', '0', 'paid'], true);

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
            'stub' => true,
        ]);
    }

    public function reconcile(PaymentProviderConnection $connection, ?\DateTimeInterface $from = null, ?\DateTimeInterface $to = null): PaymentReconciliationResult
    {
        return new PaymentReconciliationResult(true, 'completed', [
            'from' => $from?->format(DATE_ATOM),
            'to' => $to?->format(DATE_ATOM),
        ], [
            'stub' => true,
        ]);
    }
}
