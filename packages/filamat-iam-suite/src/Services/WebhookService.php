<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services;

use Filamat\IamSuite\Jobs\DeliverWebhookJob;
use Filamat\IamSuite\Models\Webhook;
use Filamat\IamSuite\Models\WebhookDelivery;
use Filamat\IamSuite\Models\WebhookNonce;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class WebhookService
{
    public function queue(Webhook $webhook, array $payload): WebhookDelivery
    {
        $idempotencyKey = (string) Str::uuid();
        $payload['idempotency_key'] ??= $idempotencyKey;

        $delivery = WebhookDelivery::query()->create([
            'webhook_id' => $webhook->getKey(),
            'status' => 'queued',
            'idempotency_key' => $idempotencyKey,
            'request' => $payload,
            'attempts' => 0,
        ]);

        DeliverWebhookJob::dispatch($delivery->getKey());

        return $delivery;
    }

    public function deliver(WebhookDelivery $delivery): void
    {
        $webhook = $delivery->webhook;
        if (! $webhook || ! $webhook->enabled) {
            $delivery->update(['status' => 'skipped']);

            return;
        }

        $payload = $delivery->request ?? [];
        $timestamp = time();
        $nonce = Str::random(16);
        $signature = $this->sign($webhook->secret, $payload, $timestamp, $nonce);

        $response = Http::timeout(20)
            ->withHeaders([
                config('filamat-iam.webhooks.signature_header') => $signature,
                config('filamat-iam.webhooks.timestamp_header') => (string) $timestamp,
                config('filamat-iam.webhooks.nonce_header') => $nonce,
            ])
            ->post($webhook->url, $payload);

        $delivery->update([
            'status' => $response->successful() ? 'delivered' : 'failed',
            'response' => [
                'status' => $response->status(),
                'body' => $response->json() ?? $response->body(),
            ],
            'attempts' => $delivery->attempts + 1,
            'last_attempt_at' => now(),
        ]);
    }

    public function verifySignature(string $secret, array $payload, string $signature, int $timestamp, string $nonce, ?string $source = null, ?int $webhookId = null): bool
    {
        $expected = $this->sign($secret, $payload, $timestamp, $nonce);

        if (! hash_equals($expected, $signature)) {
            return false;
        }

        $tolerance = (int) config('filamat-iam.webhooks.tolerance_seconds', 300);
        if (abs(time() - $timestamp) > $tolerance) {
            return false;
        }

        if ((bool) config('filamat-iam.webhooks.replay_protection', true)) {
            $source ??= 'generic';

            if (WebhookNonce::query()->where('source', $source)->where('nonce', $nonce)->exists()) {
                return false;
            }

            WebhookNonce::query()->create([
                'webhook_id' => $webhookId,
                'source' => $source,
                'nonce' => $nonce,
                'timestamp' => $timestamp,
            ]);

            $this->pruneNonces($source);
        }

        return true;
    }

    protected function sign(string $secret, array $payload, int $timestamp, string $nonce): string
    {
        $body = json_encode($payload, JSON_UNESCAPED_UNICODE);
        $data = $timestamp.'.'.$nonce.'.'.$body;

        return hash_hmac('sha256', $data, $secret);
    }

    protected function pruneNonces(string $source): void
    {
        $ttl = (int) config('filamat-iam.webhooks.nonce_ttl_seconds', 600);
        $cutoff = time() - $ttl;

        WebhookNonce::query()
            ->where('source', $source)
            ->where('timestamp', '<', $cutoff)
            ->delete();
    }
}
