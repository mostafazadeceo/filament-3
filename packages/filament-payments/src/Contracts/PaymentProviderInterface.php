<?php

namespace Haida\FilamentPayments\Contracts;

use Haida\FilamentPayments\DTO\PaymentIntentResponse;
use Haida\FilamentPayments\DTO\PaymentReconciliationResult;
use Haida\FilamentPayments\DTO\PaymentRefundResult;
use Haida\FilamentPayments\DTO\PaymentVerificationResult;
use Haida\FilamentPayments\DTO\PaymentWebhookResult;
use Haida\FilamentPayments\Models\PaymentIntent;
use Haida\FilamentPayments\Models\PaymentProviderConnection;

interface PaymentProviderInterface
{
    public function key(): string;

    /**
     * @param  array<string, mixed>  $payload
     */
    public function createIntent(PaymentIntent $intent, array $payload = []): PaymentIntentResponse;

    public function redirectUrl(PaymentIntent $intent): ?string;

    /**
     * @param  array<string, mixed>  $payload
     */
    public function verifyCallback(array $payload, PaymentProviderConnection $connection): PaymentVerificationResult;

    /**
     * @param  array<string, mixed>  $headers
     * @param  array<string, mixed>  $payload
     */
    public function handleWebhook(array $headers, array $payload, PaymentProviderConnection $connection): PaymentWebhookResult;

    /**
     * @param  array<string, mixed>  $payload
     */
    public function refund(PaymentIntent $intent, float $amount, array $payload = []): PaymentRefundResult;

    public function reconcile(PaymentProviderConnection $connection, ?\DateTimeInterface $from = null, ?\DateTimeInterface $to = null): PaymentReconciliationResult;
}
