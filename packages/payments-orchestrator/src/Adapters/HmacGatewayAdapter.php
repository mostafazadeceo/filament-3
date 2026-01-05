<?php

declare(strict_types=1);

namespace Haida\PaymentsOrchestrator\Adapters;

use Haida\PaymentsOrchestrator\Contracts\GatewayAdapterInterface;
use Haida\PaymentsOrchestrator\DTO\GatewayIntentResponse;
use Haida\PaymentsOrchestrator\DTO\GatewayWebhookEvent;
use Haida\PaymentsOrchestrator\Models\PaymentGatewayConnection;
use Haida\PaymentsOrchestrator\Models\PaymentIntent;
use Haida\PaymentsOrchestrator\Support\WebhookSignature;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use InvalidArgumentException;

class HmacGatewayAdapter implements GatewayAdapterInterface
{
    public function __construct(protected WebhookSignature $signature) {}

    public function key(): string
    {
        return 'hmac';
    }

    public function createIntent(PaymentIntent $intent, array $payload): GatewayIntentResponse
    {
        if ((bool) config('payments-orchestrator.fake')) {
            $runId = (string) (config('payments-orchestrator.fake_run_id') ?: Str::upper(Str::random(6)));

            return new GatewayIntentResponse(
                'ref-'.$runId,
                'https://gateway.test/redirect/'.$runId,
                'requires_action',
                [
                    'scenario' => 'fake',
                ]
            );
        }

        $connectionId = $payload['connection_id'] ?? Arr::get($intent->meta, 'connection_id');
        $connection = $connectionId
            ? PaymentGatewayConnection::query()->find($connectionId)
            : PaymentGatewayConnection::query()
                ->where('tenant_id', $intent->tenant_id)
                ->where('provider_key', $intent->provider_key)
                ->where('is_active', true)
                ->first();

        if (! $connection) {
            throw new InvalidArgumentException('Gateway connection not found.');
        }

        $settings = $connection->settings ?? [];
        $createUrl = $settings['create_url'] ?? null;
        if (! $createUrl) {
            $baseUrl = $settings['base_url'] ?? null;
            $path = $settings['create_path'] ?? '/payment-intents';
            if ($baseUrl) {
                $createUrl = rtrim($baseUrl, '/').'/'.ltrim($path, '/');
            }
        }

        if (! $createUrl) {
            throw new InvalidArgumentException('Gateway create URL is not configured.');
        }

        $body = [
            'intent_id' => $intent->getKey(),
            'order_id' => $intent->order_id,
            'amount' => (float) $intent->amount,
            'currency' => $intent->currency,
            'return_url' => $payload['return_url'] ?? null,
            'meta' => $payload['meta'] ?? null,
        ];

        $rawBody = json_encode($body, JSON_UNESCAPED_UNICODE);
        if ($rawBody === false) {
            throw new InvalidArgumentException('Invalid payload encoding.');
        }

        $timestamp = time();
        $secret = (string) ($connection->api_secret ?? '');
        $signature = $secret !== '' ? hash_hmac('sha256', $timestamp.'.'.$rawBody, $secret) : null;

        $response = Http::withHeaders([
            'X-Api-Key' => (string) ($connection->api_key ?? ''),
            'X-Timestamp' => $timestamp,
            'X-Signature' => $signature ?? '',
        ])->post($createUrl, $body);

        if (! $response->ok()) {
            throw new InvalidArgumentException('Gateway returned an error.');
        }

        $data = $response->json();
        if (! is_array($data)) {
            throw new InvalidArgumentException('Invalid gateway response.');
        }

        $reference = (string) ($data['reference'] ?? '');
        $redirectUrl = (string) ($data['redirect_url'] ?? '');
        $status = (string) ($data['status'] ?? 'requires_action');
        $meta = is_array($data['meta'] ?? null) ? $data['meta'] : [];

        return new GatewayIntentResponse($reference, $redirectUrl, $status, $meta);
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
