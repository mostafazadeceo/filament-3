<?php

namespace Haida\FilamentAiCore\Providers;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentAiCore\Contracts\AiProviderInterface;
use Haida\FilamentAiCore\DataTransferObjects\AiResult;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class N8nAiProvider implements AiProviderInterface
{
    public function generate(string $prompt, array $context = [], array $options = []): AiResult
    {
        return $this->request('generate', ['prompt' => $prompt], $context, $options);
    }

    public function summarize(string $text, array $schema = [], array $context = [], array $options = []): AiResult
    {
        return $this->request('summarize', ['text' => $text, 'schema' => $schema], $context, $options);
    }

    public function extractActionItems(string $text, array $schema = [], array $context = [], array $options = []): AiResult
    {
        return $this->request('extract_action_items', ['text' => $text, 'schema' => $schema], $context, $options);
    }

    public function classify(array $payload, array $taxonomy = [], array $context = [], array $options = []): AiResult
    {
        return $this->request('classify', ['payload' => $payload, 'taxonomy' => $taxonomy], $context, $options);
    }

    public function generateAgenda(array $meetingContext, array $constraints = [], array $context = [], array $options = []): AiResult
    {
        return $this->request('generate_agenda', ['meeting_context' => $meetingContext, 'constraints' => $constraints], $context, $options);
    }

    public function generateMinutes(array $transcript, array $meetingContext = [], array $context = [], array $options = []): AiResult
    {
        return $this->request('generate_minutes', ['transcript' => $transcript, 'meeting_context' => $meetingContext], $context, $options);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>  $context
     * @param  array<string, mixed>  $options
     */
    protected function request(string $action, array $payload, array $context, array $options): AiResult
    {
        $settings = $this->resolveSettings();

        if ($settings['base_url'] === '' || $settings['secret'] === '') {
            return AiResult::failure('n8n', 'n8n_not_configured');
        }

        $idempotencyKey = (string) ($options['idempotency_key'] ?? Str::uuid());

        $body = [
            'action' => $action,
            'payload' => $payload,
            'context' => $context,
            'options' => $options,
            'idempotency_key' => $idempotencyKey,
        ];

        $timestamp = time();
        $nonce = Str::random(16);
        $signature = $this->sign($settings['secret'], $body, $timestamp, $nonce);

        $headers = [
            $settings['signature_header'] => $signature,
            $settings['timestamp_header'] => (string) $timestamp,
            $settings['nonce_header'] => $nonce,
            $settings['idempotency_header'] => $idempotencyKey,
        ];

        $response = Http::timeout($settings['timeout'])
            ->withHeaders($headers)
            ->post($settings['base_url'], $body);

        if (! $response->successful()) {
            return AiResult::failure('n8n', 'n8n_http_error', [$response->body()]);
        }

        if (! $this->verifyInbound($response, $settings)) {
            return AiResult::failure('n8n', 'n8n_signature_invalid');
        }

        $data = $response->json();
        if (! is_array($data)) {
            return AiResult::failure('n8n', 'n8n_invalid_response');
        }

        if (! ($data['ok'] ?? true)) {
            return AiResult::failure('n8n', (string) ($data['error'] ?? 'n8n_error'), (array) ($data['warnings'] ?? []));
        }

        return AiResult::success(
            'n8n',
            $data['model'] ?? null,
            $data['output_text'] ?? null,
            $data['output_json'] ?? null,
            isset($data['tokens']) ? (int) $data['tokens'] : null,
            isset($data['latency_ms']) ? (int) $data['latency_ms'] : null,
            (array) ($data['warnings'] ?? [])
        );
    }

    /**
     * @return array{base_url: string, secret: string, timeout: int, signature_header: string, timestamp_header: string, nonce_header: string, idempotency_header: string, tolerance_seconds: int, nonce_ttl_seconds: int}
     */
    protected function resolveSettings(): array
    {
        $tenant = TenantContext::getTenant();
        $tenantSettings = $tenant instanceof Tenant ? (array) data_get($tenant->settings, 'ai.n8n', []) : [];

        return [
            'base_url' => (string) ($tenantSettings['base_url'] ?? config('filament-ai-core.providers.n8n.base_url', '')),
            'secret' => (string) ($tenantSettings['secret'] ?? config('filament-ai-core.providers.n8n.secret', '')),
            'timeout' => (int) config('filament-ai-core.providers.n8n.timeout', 15),
            'signature_header' => (string) config('filament-ai-core.providers.n8n.signature_header', 'X-Filamat-Signature'),
            'timestamp_header' => (string) config('filament-ai-core.providers.n8n.timestamp_header', 'X-Filamat-Timestamp'),
            'nonce_header' => (string) config('filament-ai-core.providers.n8n.nonce_header', 'X-Filamat-Nonce'),
            'idempotency_header' => (string) config('filament-ai-core.providers.n8n.idempotency_header', 'X-Idempotency-Key'),
            'tolerance_seconds' => (int) config('filament-ai-core.providers.n8n.tolerance_seconds', 300),
            'nonce_ttl_seconds' => (int) config('filament-ai-core.providers.n8n.nonce_ttl_seconds', 600),
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function sign(string $secret, array $payload, int $timestamp, string $nonce): string
    {
        $body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $data = $timestamp.'.'.$nonce.'.'.$body;

        return hash_hmac('sha256', $data ?: '', $secret);
    }

    /**
     * @param  array{signature_header: string, timestamp_header: string, nonce_header: string, tolerance_seconds: int, nonce_ttl_seconds: int, secret: string}  $settings
     */
    protected function verifyInbound(Response $response, array $settings): bool
    {
        $signature = (string) $response->header($settings['signature_header'], '');
        $timestamp = (int) $response->header($settings['timestamp_header'], 0);
        $nonce = (string) $response->header($settings['nonce_header'], '');

        if ($signature === '' || $timestamp === 0 || $nonce === '') {
            return true;
        }

        if (abs(time() - $timestamp) > $settings['tolerance_seconds']) {
            return false;
        }

        $payload = $response->json();
        if (! is_array($payload)) {
            return false;
        }

        $expected = $this->sign($settings['secret'], $payload, $timestamp, $nonce);
        if (! hash_equals($expected, $signature)) {
            return false;
        }

        return $this->storeNonce($nonce, $settings['nonce_ttl_seconds']);
    }

    protected function storeNonce(string $nonce, int $ttl): bool
    {
        $key = 'ai:n8n:nonce:'.$nonce;

        return Cache::add($key, true, $ttl);
    }
}
