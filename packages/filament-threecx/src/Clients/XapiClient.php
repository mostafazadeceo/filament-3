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
use Illuminate\Support\Str;
use Throwable;

class XapiClient implements ThreeCxClientInterface
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
    public function listContacts(array $query = []): array
    {
        $path = (string) config('filament-threecx.xapi.contacts_path', '/contacts');

        return $this->request('GET', $path, $this->normalizeOdataQuery($query));
    }

    /**
     * @param  array<string, mixed>  $query
     */
    public function searchContacts(string $term, array $query = []): array
    {
        $query['search'] = $term;

        return $this->listContacts($query);
    }

    /**
     * @param  array<string, mixed>  $query
     */
    public function listCallHistory(array $query = []): array
    {
        $path = (string) config('filament-threecx.xapi.call_history_path', '/call-history');

        return $this->request('GET', $path, $this->normalizeOdataQuery($query));
    }

    public function health(): array
    {
        $path = (string) config('filament-threecx.xapi.health_path', '/health');

        return $this->request('GET', $path);
    }

    public function version(): array
    {
        $path = (string) config('filament-threecx.xapi.version_path', '/version');

        return $this->request('GET', $path);
    }

    public function capabilities(): array
    {
        $path = (string) config('filament-threecx.xapi.capabilities_path', '/capabilities');

        return $this->request('GET', $path);
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
        $query = $this->normalizeOdataQuery($query);
        $fullPath = $this->buildPath($path);
        $rateKey = 'threecx:xapi:'.$this->instance->getKey();

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
                'api_area' => 'xapi',
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
                'api_area' => 'xapi',
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
        return (string) config('filament-threecx.auth.scopes.xapi', 'xapi');
    }

    protected function buildPath(string $path): string
    {
        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        $basePath = trim((string) config('filament-threecx.xapi.base_path', '/xapi'), '/');
        $path = ltrim($path, '/');

        return $basePath === '' ? '/'.$path : '/'.$basePath.'/'.$path;
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    protected function normalizeOdataQuery(array $query): array
    {
        $keys = ['top', 'skip', 'filter', 'select', 'expand', 'orderby', 'count', 'search'];

        foreach ($keys as $key) {
            if (array_key_exists($key, $query)) {
                $query['$'.$key] = $query[$key];
                unset($query[$key]);
            }
        }

        return $query;
    }
}
