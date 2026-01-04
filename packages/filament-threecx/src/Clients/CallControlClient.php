<?php

namespace Haida\FilamentThreeCx\Clients;

use Haida\FilamentThreeCx\Contracts\ThreeCxClientInterface;
use Haida\FilamentThreeCx\Exceptions\ThreeCxApiException;
use Haida\FilamentThreeCx\Exceptions\ThreeCxRateLimitException;
use Haida\FilamentThreeCx\Models\ThreeCxInstance;
use Haida\FilamentThreeCx\Services\ThreeCxApiAuditLogger;
use Haida\FilamentThreeCx\Services\ThreeCxAuthService;
use Haida\FilamentThreeCx\Services\ThreeCxHttp;
use Haida\FilamentThreeCx\Support\ThreeCxRateLimiter;
use Throwable;

class CallControlClient implements ThreeCxClientInterface
{
    protected ?int $lastStatusCode = null;

    protected ?int $lastDurationMs = null;

    public function __construct(
        protected ThreeCxInstance $instance,
        protected ThreeCxHttp $http,
        protected ThreeCxAuthService $auth,
        protected ThreeCxApiAuditLogger $auditLogger,
        protected ThreeCxRateLimiter $rateLimiter,
    ) {}

    /**
     * @param  array<string, mixed>  $query
     * @param  array<string, mixed>|null  $body
     * @return array<string, mixed>
     */
    public function request(string $method, string $path, array $query = [], ?array $body = null): array
    {
        return $this->requestWithRetry($method, $path, $query, $body, true);
    }

    /**
     * @param  array<string, mixed>  $query
     */
    public function listEntityStates(array $query = []): array
    {
        $path = (string) config('filament-threecx.call_control.entities_path', '/entities');

        return $this->request('GET', $path, $query);
    }

    public function getDnState(string $dn): array
    {
        $template = (string) config('filament-threecx.call_control.dn_state_path', '/dn/{dn}');

        return $this->request('GET', $this->resolvePath($template, ['dn' => $dn]));
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function makeCall(string $from, string $to, array $payload = []): array
    {
        $path = (string) config('filament-threecx.call_control.calls_path', '/calls');
        $fromKey = (string) config('filament-threecx.call_control.from_key', 'from');
        $toKey = (string) config('filament-threecx.call_control.to_key', 'to');

        $payload = array_merge([
            $fromKey => $from,
            $toKey => $to,
        ], $payload);

        return $this->request('POST', $path, [], $payload);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function transferCall(string $callId, array $payload = []): array
    {
        $template = (string) config('filament-threecx.call_control.call_transfer_path', '/calls/{call}/transfer');

        return $this->request('POST', $this->resolvePath($template, ['call' => $callId]), [], $payload);
    }

    public function terminateCall(string $callId): array
    {
        $template = (string) config('filament-threecx.call_control.call_terminate_path', '/calls/{call}');

        return $this->request('DELETE', $this->resolvePath($template, ['call' => $callId]));
    }

    public function lastStatusCode(): ?int
    {
        return $this->lastStatusCode;
    }

    public function lastDurationMs(): ?int
    {
        return $this->lastDurationMs;
    }

    /**
     * @param  array<string, mixed>  $query
     * @param  array<string, mixed>|null  $body
     * @return array<string, mixed>
     */
    protected function requestWithRetry(string $method, string $path, array $query, ?array $body, bool $allowRetry): array
    {
        $fullPath = $this->buildPath($path);
        $rateKey = 'threecx:call_control:'.$this->instance->getKey();

        $maxRequests = (int) config('filament-threecx.rate_limit.max_requests', 60);
        $perSeconds = (int) config('filament-threecx.rate_limit.per_seconds', 1);
        $this->rateLimiter->throttle($rateKey, $maxRequests, $perSeconds);

        $startedAt = microtime(true);

        try {
            $token = $this->auth->getAccessToken($this->instance, $this->resolveScope());
            $response = $this->http->request($this->instance, $method, $fullPath, $query, $body, $token);
            $durationMs = (int) round((microtime(true) - $startedAt) * 1000);

            $this->lastStatusCode = $response->status();
            $this->lastDurationMs = $durationMs;

            $this->auditLogger->log($this->instance, [
                'api_area' => 'call_control',
                'method' => strtoupper($method),
                'path' => $fullPath,
                'status_code' => $response->status(),
                'duration_ms' => $durationMs,
                'metadata' => [
                    'query' => $query,
                    'request_body' => $body,
                    'response_body' => $response->json(),
                ],
            ]);

            if ($response->status() === 401 && $allowRetry) {
                $this->auth->getAccessToken($this->instance, $this->resolveScope(), true);

                return $this->requestWithRetry($method, $path, $query, $body, false);
            }

            if ($response->status() === 429) {
                throw ThreeCxRateLimitException::throttled($response->status(), $response->json());
            }

            if ($response->successful() || $response->status() === 204) {
                return $response->json() ?? [];
            }

            throw ThreeCxApiException::fromResponse($response->status(), $response->json());
        } catch (Throwable $exception) {
            $durationMs = (int) round((microtime(true) - $startedAt) * 1000);
            $status = $exception instanceof ThreeCxApiException ? $exception->statusCode() : null;
            $payload = $exception instanceof ThreeCxApiException ? $exception->payload() : null;

            $this->lastStatusCode = $status;
            $this->lastDurationMs = $durationMs;

            $this->auditLogger->log($this->instance, [
                'api_area' => 'call_control',
                'method' => strtoupper($method),
                'path' => $fullPath,
                'status_code' => $status,
                'duration_ms' => $durationMs,
                'metadata' => [
                    'query' => $query,
                    'request_body' => $body,
                    'response_body' => $payload,
                    'error' => $exception->getMessage(),
                ],
            ]);

            throw $exception;
        }
    }

    protected function resolveScope(): string
    {
        return (string) config('filament-threecx.auth.scopes.call_control', 'call_control');
    }

    protected function buildPath(string $path): string
    {
        $basePath = trim((string) config('filament-threecx.call_control.base_path', '/call-control'), '/');
        $path = ltrim($path, '/');

        return $basePath === '' ? '/'.$path : '/'.$basePath.'/'.$path;
    }

    /**
     * @param  array<string, string>  $params
     */
    protected function resolvePath(string $template, array $params): string
    {
        foreach ($params as $key => $value) {
            $template = str_replace('{'.$key.'}', $value, $template);
        }

        return $template;
    }
}
