<?php

declare(strict_types=1);

namespace Haida\FilamentChat\Services\Providers;

use Haida\FilamentChat\Models\ChatConnection;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class RocketChatClient
{
    public function __construct(
        protected ChatConnection $connection,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function get(string $path, array $query = []): array
    {
        return $this->request('GET', $path, ['query' => $query]);
    }

    /**
     * @return array<string, mixed>
     */
    public function post(string $path, array $payload = []): array
    {
        return $this->request('POST', $path, ['json' => $payload]);
    }

    /**
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function request(string $method, string $path, array $options = []): array
    {
        $response = $this->client()->send($method, $this->buildUrl($path), $options);

        if (! $response->successful()) {
            $message = $response->json('error')
                ?? $response->json('message')
                ?? $response->body();
            throw new RuntimeException('Rocket.Chat API error: '.$message);
        }

        $data = $response->json();
        if (is_array($data) && array_key_exists('success', $data) && ! $data['success']) {
            $message = $data['error'] ?? $data['message'] ?? 'Rocket.Chat API error';
            throw new RuntimeException($message);
        }

        return is_array($data) ? $data : [];
    }

    protected function client(): PendingRequest
    {
        $timeout = (int) (config('filament-chat.providers.rocket_chat.timeout', 10));
        $verify = (bool) (config('filament-chat.providers.rocket_chat.verify_tls', true));

        return Http::withHeaders($this->headers())
            ->acceptJson()
            ->timeout($timeout)
            ->withOptions([
                'verify' => $verify,
            ]);
    }

    /**
     * @return array<string, string>
     */
    protected function headers(): array
    {
        $userId = (string) ($this->connection->api_user_id ?? '');
        $token = (string) ($this->connection->api_token ?? '');

        if ($userId === '' || $token === '') {
            throw new RuntimeException('Rocket.Chat credentials are missing.');
        }

        return [
            'X-User-Id' => $userId,
            'X-Auth-Token' => $token,
        ];
    }

    protected function buildUrl(string $path): string
    {
        $base = rtrim((string) $this->connection->base_url, '/');
        if ($base === '') {
            throw new RuntimeException('Rocket.Chat base URL is missing.');
        }
        $path = '/'.ltrim($path, '/');

        return $base.$path;
    }
}
