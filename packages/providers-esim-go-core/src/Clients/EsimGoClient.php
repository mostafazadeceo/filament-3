<?php

declare(strict_types=1);

namespace Haida\ProvidersEsimGoCore\Clients;

use Haida\ProvidersEsimGoCore\Exceptions\EsimGoApiException;
use Haida\ProvidersEsimGoCore\Models\EsimGoConnection;
use Haida\ProvidersEsimGoCore\Support\EsimGoCacheKey;
use Haida\ProvidersEsimGoCore\Support\EsimGoFakeResponse;
use Haida\ProvidersEsimGoCore\Support\EsimGoRateLimiter;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;
use Throwable;

class EsimGoClient
{
    public function __construct(
        protected EsimGoConnection $connection,
        protected EsimGoRateLimiter $rateLimiter,
        protected bool $sandbox = false,
    ) {}

    public function listCatalogue(array $params = []): array
    {
        if (! empty($params['__nocache'])) {
            unset($params['__nocache']);

            return $this->request('GET', 'catalogue', $params, null, 'listCatalogue');
        }

        return $this->cached('catalogue', $params, function () use ($params) {
            return $this->request('GET', 'catalogue', $params, null, 'listCatalogue');
        });
    }

    public function createOrder(array $payload): array
    {
        return $this->request('POST', 'orders', [], $payload, 'createOrder');
    }

    public function listOrders(array $params = []): array
    {
        return $this->request('GET', 'orders', $params, null, 'listOrders');
    }

    public function findOrder(string $reference): array
    {
        return $this->request('GET', "orders/{$reference}", [], null, 'findOrder');
    }

    public function listInventory(array $params = []): array
    {
        return $this->request('GET', 'inventory', $params, null, 'listInventory');
    }

    public function refundInventory(array $payload): array
    {
        return $this->request('POST', 'inventory/refund', [], $payload, 'refundInventory');
    }

    public function listAssignments(array $params = []): array
    {
        return $this->request('GET', 'esims/assignments', $params, null, 'listAssignments');
    }

    public function paginateCatalogue(array $params = []): LazyCollection
    {
        return LazyCollection::make(function () use ($params) {
            $page = (int) ($params['page'] ?? 1);
            $perPage = (int) ($params['perPage'] ?? config('providers-esim-go-core.catalogue.per_page', 100));

            while (true) {
                $response = $this->listCatalogue(array_merge($params, [
                    'page' => $page,
                    'perPage' => $perPage,
                    'pageSize' => $params['pageSize'] ?? $perPage,
                ]));

                $items = $response['data']
                    ?? $response['items']
                    ?? $response['catalogue']
                    ?? $response['bundles']
                    ?? [];
                foreach ($items as $item) {
                    yield $item;
                }

                $pagination = $response['pagination'] ?? [];
                $totalPages = (int) ($pagination['totalPages'] ?? $pagination['pages'] ?? 0);
                if ($totalPages === 0) {
                    $totalPages = (int) ($response['pageCount'] ?? 0);
                }

                if ($totalPages > 0 && $page >= $totalPages) {
                    break;
                }

                if ($totalPages === 0) {
                    $pageSize = (int) ($response['pageSize'] ?? $perPage);
                    if (count($items) < $pageSize) {
                        break;
                    }
                }

                $page++;
            }
        });
    }

    protected function cached(string $prefix, array $params, callable $callback): array
    {
        $cacheSeconds = (int) config('providers-esim-go-core.catalogue.cache_seconds', 3600);
        if ($cacheSeconds <= 0) {
            return $callback();
        }

        $key = EsimGoCacheKey::make($prefix.':'.$this->connection->getKey(), $params);
        $store = config('providers-esim-go-core.cache.store');
        $cache = $store ? Cache::store($store) : Cache::store();

        return $cache->remember($key, $cacheSeconds, $callback);
    }

    protected function request(string $method, string $resource, array $query = [], ?array $payload = null, ?string $endpointName = null): array
    {
        if ((bool) config('providers-esim-go-core.fake')) {
            return EsimGoFakeResponse::handle($method, $resource, $query, $payload);
        }

        $url = $this->buildUrl($resource);

        $rateLimitKey = 'esim-go:'.$this->connection->getKey();
        $rateConfig = config('providers-esim-go-core.rate_limit', []);
        $maxRequests = (int) ($rateConfig['max_requests'] ?? 10);
        $perSeconds = (int) ($rateConfig['per_seconds'] ?? 1);

        $this->rateLimiter->throttle($rateLimitKey.':'.$perSeconds, $maxRequests);

        $startedAt = microtime(true);

        try {
            $response = $this->send($method, $url, $query, $payload);
            $durationMs = (int) round((microtime(true) - $startedAt) * 1000);

            $this->logRequest($method, $url, $endpointName, $payload, $response, $durationMs, null);

            if ($response->successful() || $response->status() === 204) {
                return $response->json() ?? [];
            }

            throw EsimGoApiException::fromResponse($response->status(), $response->json());
        } catch (Throwable $exception) {
            $durationMs = (int) round((microtime(true) - $startedAt) * 1000);
            $status = $exception instanceof EsimGoApiException ? $exception->statusCode() : null;
            $payload = $exception instanceof EsimGoApiException ? $exception->payload() : null;

            $this->logRequest($method, $url, $endpointName, $payload, null, $durationMs, $exception->getMessage(), $status);

            if ($exception instanceof EsimGoApiException) {
                throw $exception;
            }

            throw EsimGoApiException::fromResponse(null, null, 'ارتباط با eSIM Go ناموفق بود.');
        }
    }

    protected function send(string $method, string $url, array $query = [], ?array $payload = null): Response
    {
        $timeout = (int) config('providers-esim-go-core.http.timeout_seconds', 40);
        $retryTimes = (int) config('providers-esim-go-core.http.retry_times', 2);
        $retrySleep = (int) config('providers-esim-go-core.http.retry_sleep_ms', 500);

        return $this->client()
            ->retry($retryTimes, function (int $attempt, Throwable $exception) use ($retrySleep): int {
                if ($exception instanceof RequestException) {
                    $response = $exception->response;
                    if ($response && $response->status() === 503) {
                        $retryAfter = $response->header('Retry-After');
                        if (is_numeric($retryAfter)) {
                            return (int) $retryAfter * 1000;
                        }
                    }
                }

                return $retrySleep * max(1, $attempt);
            }, function (Throwable $exception): bool {
                if ($exception instanceof RequestException) {
                    $status = $exception->response?->status();

                    return in_array($status, [429, 503], true);
                }

                return true;
            })
            ->timeout($timeout)
            ->send($method, $url, [
                'query' => $query,
                'json' => $payload,
            ]);
    }

    protected function client(): PendingRequest
    {
        $apiKeyHeader = (string) config('providers-esim-go-core.api_key_header', 'X-API-Key');
        $correlationId = app()->bound('correlation_id') ? app('correlation_id') : null;

        $headers = [
            'Accept' => 'application/json',
            $apiKeyHeader => $this->connection->api_key,
        ];

        if (is_string($correlationId) && $correlationId !== '') {
            $headers['X-Correlation-Id'] = $correlationId;
        }

        return Http::withHeaders($headers);
    }

    protected function buildUrl(string $resource): string
    {
        $base = $this->sandbox
            ? config('providers-esim-go-core.sandbox_base_url', 'https://api.esim-go.com/v2.5')
            : config('providers-esim-go-core.base_url', 'https://api.esim-go.com/v2.5');

        return Str::of((string) $base)->rtrim('/')->append('/')->append(ltrim($resource, '/'))->toString();
    }

    protected function logRequest(
        string $method,
        string $url,
        ?string $endpointName,
        ?array $payload,
        ?Response $response,
        int $durationMs,
        ?string $error = null,
        ?int $statusOverride = null,
    ): void {
        if (! (bool) config('providers-esim-go-core.logging.enabled', true)) {
            return;
        }

        logger()->info('esim-go.http', [
            'connection_id' => $this->connection->getKey(),
            'method' => strtoupper($method),
            'url' => $url,
            'endpoint' => $endpointName,
            'request_body' => $payload,
            'status' => $statusOverride ?? $response?->status(),
            'response' => $response?->json(),
            'duration_ms' => $durationMs,
            'error' => $error,
        ]);
    }
}
