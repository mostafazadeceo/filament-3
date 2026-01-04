<?php

namespace Haida\PaymentsOrchestrator\Contracts;

use Haida\PaymentsOrchestrator\DTO\GatewayIntentResponse;
use Haida\PaymentsOrchestrator\DTO\GatewayWebhookEvent;
use Haida\PaymentsOrchestrator\Models\PaymentGatewayConnection;
use Haida\PaymentsOrchestrator\Models\PaymentIntent;

interface GatewayAdapterInterface
{
    public function key(): string;

    /**
     * @param  array<string, mixed>  $payload
     */
    public function createIntent(PaymentIntent $intent, array $payload): GatewayIntentResponse;

    /**
     * @param  array<string, mixed>  $headers
     */
    public function verifyWebhook(string $payload, array $headers, PaymentGatewayConnection $connection): bool;

    /**
     * @param  array<string, mixed>  $headers
     */
    public function parseWebhook(string $payload, array $headers): GatewayWebhookEvent;
}
