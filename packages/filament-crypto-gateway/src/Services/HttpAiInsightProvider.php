<?php

namespace Haida\FilamentCryptoGateway\Services;

use Haida\FilamentCryptoGateway\Contracts\AiInsightProvider;
use Illuminate\Support\Facades\Http;

class HttpAiInsightProvider implements AiInsightProvider
{
    /**
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    public function generateInsights(array $context): array
    {
        if (! (bool) config('filament-crypto-gateway.ai.enabled', false)) {
            return [];
        }

        $url = (string) (config('filament-crypto-gateway.ai.n8n.url') ?? config('filament-crypto-gateway.ai.webhook_url', ''));
        $secret = (string) (config('filament-crypto-gateway.ai.n8n.secret') ?? config('filament-crypto-gateway.ai.secret', ''));
        $timeout = (int) (config('filament-crypto-gateway.ai.n8n.timeout') ?? config('filament-crypto-gateway.ai.timeout', 10));

        if ($url === '' || $secret === '') {
            return [];
        }

        $payload = json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '{}';
        $timestamp = (string) time();
        $signature = hash_hmac('sha256', $timestamp.'.'.$payload, $secret);

        $response = Http::timeout($timeout)
            ->withHeaders([
                'X-Crypto-Timestamp' => $timestamp,
                'X-Crypto-Signature' => $signature,
                'Content-Type' => 'application/json',
            ])
            ->post($url, $context);

        if (! $response->ok()) {
            return [];
        }

        $data = $response->json();

        return is_array($data) ? $data : [];
    }
}
