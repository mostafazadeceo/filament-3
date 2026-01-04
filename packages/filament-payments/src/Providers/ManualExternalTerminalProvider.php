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
use Illuminate\Support\Str;

class ManualExternalTerminalProvider implements PaymentProviderInterface
{
    public function key(): string
    {
        return 'manual';
    }

    public function createIntent(PaymentIntent $intent, array $payload = []): PaymentIntentResponse
    {
        $reference = $payload['reference'] ?? ('MANUAL-'.Str::upper(Str::random(8)));

        return new PaymentIntentResponse(
            $reference,
            null,
            $payload['status'] ?? 'pending',
            ['source' => 'external_terminal']
        );
    }

    public function redirectUrl(PaymentIntent $intent): ?string
    {
        return null;
    }

    public function verifyCallback(array $payload, PaymentProviderConnection $connection): PaymentVerificationResult
    {
        $status = $payload['status'] ?? 'pending';
        $verified = in_array($status, ['confirmed', 'captured', 'paid'], true);

        return new PaymentVerificationResult(
            $verified,
            $status,
            $payload['reference'] ?? null,
            $payload
        );
    }

    public function handleWebhook(array $headers, array $payload, PaymentProviderConnection $connection): PaymentWebhookResult
    {
        $status = $payload['status'] ?? 'pending';
        $processed = in_array($status, ['confirmed', 'captured', 'paid'], true);

        return new PaymentWebhookResult(
            $processed,
            $processed ? 'processed' : 'ignored',
            $payload['reference'] ?? null,
            $payload['type'] ?? null,
            $payload
        );
    }

    public function refund(PaymentIntent $intent, float $amount, array $payload = []): PaymentRefundResult
    {
        return new PaymentRefundResult(true, 'refunded', $payload['reference'] ?? null, [
            'amount' => $amount,
        ]);
    }

    public function reconcile(PaymentProviderConnection $connection, ?\DateTimeInterface $from = null, ?\DateTimeInterface $to = null): PaymentReconciliationResult
    {
        return new PaymentReconciliationResult(true, 'completed', [
            'from' => $from?->format(DATE_ATOM),
            'to' => $to?->format(DATE_ATOM),
        ]);
    }
}
