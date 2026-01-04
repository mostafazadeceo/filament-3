<?php

namespace Haida\PaymentsOrchestrator\Adapters;

use Haida\PaymentsOrchestrator\Contracts\GatewayAdapterInterface;
use Haida\PaymentsOrchestrator\DTO\GatewayIntentResponse;
use Haida\PaymentsOrchestrator\DTO\GatewayWebhookEvent;
use Haida\PaymentsOrchestrator\Models\PaymentGatewayConnection;
use Haida\PaymentsOrchestrator\Models\PaymentIntent;
use Haida\PaymentsOrchestrator\Support\WebhookSignature;
use InvalidArgumentException;

class DummyGatewayAdapter implements GatewayAdapterInterface
{
    public function __construct(protected WebhookSignature $signature)
    {
    }

    public function key(): string
    {
        return 'dummy';
    }

    public function createIntent(PaymentIntent $intent, array $payload): GatewayIntentResponse
    {
        $reference = 'dummy-' . $intent->getKey();
        $redirectUrl = 'https://example.test/pay/' . $intent->getKey();

        return new GatewayIntentResponse($reference, $redirectUrl, 'requires_action');
    }

    public function verifyWebhook(string $payload, array $headers, PaymentGatewayConnection $connection): bool
    {
        $secret = (string) $connection->webhook_secret;
        if ($secret === '') {
            return false;
        }

        return $this->signature->verify($payload, $headers, $secret);
    }

    public function parseWebhook(string $payload, array $headers): GatewayWebhookEvent
    {
        $decoded = json_decode($payload, true);
        if (! is_array($decoded)) {
            throw new InvalidArgumentException('Invalid webhook payload.');
        }

        return new GatewayWebhookEvent(
            (string) ($decoded['event_id'] ?? ''),
            (string) ($decoded['status'] ?? 'pending'),
            isset($decoded['intent_id']) ? (int) $decoded['intent_id'] : null,
            isset($decoded['order_id']) ? (int) $decoded['order_id'] : null,
            isset($decoded['amount']) ? (float) $decoded['amount'] : null,
            $decoded['currency'] ?? null,
            $decoded['reference'] ?? null,
            $decoded
        );
    }
}
