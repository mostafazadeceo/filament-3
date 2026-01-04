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

    public function deliverNow(Webhook $webhook, array $payload): WebhookDelivery
    {
        $idempotencyKey = (string) ($payload['idempotency_key'] ?? Str::uuid());
        $payload['idempotency_key'] ??= $idempotencyKey;

        $delivery = WebhookDelivery::query()->create([
            'webhook_id' => $webhook->getKey(),
            'status' => 'queued',
            'idempotency_key' => $idempotencyKey,
            'request' => $payload,
            'attempts' => 0,
        ]);

        $this->deliver($delivery);

        return $delivery->refresh();
    }

    public function deliver(WebhookDelivery $delivery): void
    {
        $webhook = $delivery->webhook;
        if (! $webhook || ! $webhook->enabled) {
            $delivery->update(['status' => 'skipped']);

            return;
        }

        $payload = $delivery->request ?? [];

        $response = Http::timeout(20)
            ->withHeaders($this->buildHeaders($webhook, $payload))
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

    /**
     * @return array<string, string>
     */
    protected function buildHeaders(Webhook $webhook, array $payload): array
    {
        $authMode = $webhook->auth_mode ?: config('filamat-iam.automation.default_auth_mode', 'hmac+nonce');
        $headers = is_array($webhook->headers_static ?? null) ? $webhook->headers_static : [];
        $cleanHeaders = [];

        foreach ($headers as $key => $value) {
            if (! is_string($key) || $key === '' || $value === null || $value === '') {
                continue;
            }
            $cleanHeaders[$key] = is_scalar($value) ? (string) $value : json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        if ($authMode === 'hmac+nonce') {
            $timestamp = time();
            $nonce = Str::random(16);
            $signature = $this->sign((string) $webhook->secret, $payload, $timestamp, $nonce);

            $signatureHeader = (string) config('filamat-iam.webhooks.signature_header', 'X-Filamat-Signature');
            $timestampHeader = (string) config('filamat-iam.webhooks.timestamp_header', 'X-Filamat-Timestamp');
            $nonceHeader = (string) config('filamat-iam.webhooks.nonce_header', 'X-Filamat-Nonce');

            if ($signatureHeader === '') {
                $signatureHeader = 'X-Filamat-Signature';
            }
            if ($timestampHeader === '') {
                $timestampHeader = 'X-Filamat-Timestamp';
            }
            if ($nonceHeader === '') {
                $nonceHeader = 'X-Filamat-Nonce';
            }

            $cleanHeaders[$signatureHeader] = $signature;
            $cleanHeaders[$timestampHeader] = (string) $timestamp;
            $cleanHeaders[$nonceHeader] = $nonce;

            return $cleanHeaders;
        }

        if ($authMode === 'header') {
            $headerName = $cleanHeaders['auth_header'] ?? config('filamat-iam.automation.inbound.token_header', 'X-N8N-Token');
            unset($cleanHeaders['auth_header']);
            if ($headerName === '') {
                $headerName = 'X-N8N-Token';
            }
            if ($headerName) {
                $cleanHeaders[$headerName] = (string) $webhook->secret;
            }

            return $cleanHeaders;
        }

        if ($authMode === 'basic') {
            $username = $cleanHeaders['basic_user'] ?? '';
            unset($cleanHeaders['basic_user']);
            $token = base64_encode($username.':'.(string) $webhook->secret);
            $cleanHeaders['Authorization'] = 'Basic '.$token;

            return $cleanHeaders;
        }

        if ($authMode === 'jwt') {
            $cleanHeaders['Authorization'] = 'Bearer '.(string) $webhook->secret;

            return $cleanHeaders;
        }

        return $cleanHeaders;
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
