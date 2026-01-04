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

class InternationalRedirectProvider implements PaymentProviderInterface
{
    public function key(): string
    {
        return 'intl_redirect';
    }

    public function createIntent(PaymentIntent $intent, array $payload = []): PaymentIntentResponse
    {
        $config = config('filament-payments.providers.intl_redirect', []);
        $reference = 'INTL-'.Str::upper(Str::random(8));
        $redirectUrl = Arr::get($config, 'redirect_url');

        return new PaymentIntentResponse($reference, $redirectUrl, 'pending', [
            'stub' => true,
        ]);
    }

    public function redirectUrl(PaymentIntent $intent): ?string
    {
        return $intent->redirect_url;
    }

    public function verifyCallback(array $payload, PaymentProviderConnection $connection): PaymentVerificationResult
    {
        $status = $payload['status'] ?? 'pending';
        $verified = in_array($status, ['succeeded', 'paid', 'captured'], true);

        return new PaymentVerificationResult(
            $verified,
            $verified ? 'confirmed' : 'failed',
            $payload['reference'] ?? $payload['intent'] ?? null,
            $payload
        );
    }

    public function handleWebhook(array $headers, array $payload, PaymentProviderConnection $connection): PaymentWebhookResult
    {
        $event = $payload['event'] ?? $payload['type'] ?? null;
        $status = $payload['status'] ?? 'pending';
        $processed = in_array($status, ['succeeded', 'paid', 'captured'], true) || in_array($event, ['payment_succeeded', 'charge.succeeded'], true);

        return new PaymentWebhookResult(
            $processed,
            $processed ? 'processed' : 'ignored',
            $payload['reference'] ?? $payload['intent'] ?? null,
            $event,
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
