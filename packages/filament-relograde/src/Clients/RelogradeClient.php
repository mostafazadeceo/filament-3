<?php

namespace Haida\FilamentRelograde\Clients;

use Haida\FilamentRelograde\Exceptions\RelogradeApiException;
use Haida\FilamentRelograde\Models\RelogradeConnection;
use Haida\FilamentRelograde\Services\RelogradeApiLogger;
use Haida\FilamentRelograde\Support\RelogradeCacheKey;
use Haida\FilamentRelograde\Support\RelogradeRateLimiter;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\LazyCollection;
use Throwable;

class RelogradeClient
{
    public function __construct(
        protected RelogradeConnection $connection,
        protected RelogradeRateLimiter $rateLimiter,
        protected RelogradeApiLogger $logger,
    ) {}

    public function listBrands(array $params = []): array
    {
        if (! empty($params['__nocache'])) {
            unset($params['__nocache']);

            return $this->request('GET', 'brand', $params, null, 'listBrands');
        }

        return $this->cached('brands', $params, function () use ($params) {
            return $this->request('GET', 'brand', $params, null, 'listBrands');
        });
    }

    public function listProducts(array $params = []): array
    {
        if (! empty($params['__nocache'])) {
            unset($params['__nocache']);

            return $this->request('GET', 'product', $params, null, 'listProducts');
        }

        return $this->cached('products', $params, function () use ($params) {
            return $this->request('GET', 'product', $params, null, 'listProducts');
        });
    }

    public function listAccounts(array $params = []): array
    {
        return $this->request('GET', 'account', $params, null, 'listAccounts');
    }

    public function createOrder(array $payload): array
    {
        return $this->request('POST', 'order', [], $payload, 'createOrder');
    }

    public function confirmOrder(string $trx): array
    {
        return $this->request('PATCH', "order/confirm/{$trx}", [], null, 'confirmOrder');
    }

    public function resolveOrder(string $trx): array
    {
        return $this->request('PATCH', "order/resolve/{$trx}", [], null, 'resolveOrder');
    }

    public function cancelOrder(string $trx): array
    {
        return $this->request('PATCH', "order/cancel/{$trx}", [], null, 'cancelOrder');
    }

    public function findOrder(string $trx): array
    {
        return $this->request('GET', "order/{$trx}", [], null, 'findOrder');
    }

    public function listOrders(array $params = []): array
    {
        return $this->request('GET', 'order', $params, null, 'listOrders');
    }

    public function iterateBrands(array $params = []): LazyCollection
    {
        return $this->paginate('brand', $params, 'listBrands');
    }

    public function iterateProducts(array $params = []): LazyCollection
    {
        return $this->paginate('product', $params, 'listProducts');
    }

    protected function paginate(string $resource, array $params, string $endpointName): LazyCollection
    {
        return LazyCollection::make(function () use ($resource, $params, $endpointName) {
            $page = (int) ($params['page'] ?? 1);
            $limit = (int) ($params['limit'] ?? 100);

            while (true) {
                $response = $this->request('GET', $resource, array_merge($params, [
                    'page' => $page,
                    'limit' => $limit,
                ]), null, $endpointName);

                $items = $response['data'] ?? $response;
                foreach ($items as $item) {
                    yield $item;
                }

                $pagination = $response['pagination'] ?? null;
                $pages = (int) ($pagination['pages'] ?? 0);

                if ($pages > 0 && $page >= $pages) {
                    break;
                }

                if ($pages === 0 && count($items) < $limit) {
                    break;
                }

                $page++;
            }
        });
    }

    protected function cached(string $prefix, array $params, callable $callback): array
    {
        if (! config('relograde.cache.enabled')) {
            return $callback();
        }

        $key = RelogradeCacheKey::make($prefix.':'.$this->connection->getKey(), $params);
        $store = config('relograde.cache.store');
        $ttl = (int) config('relograde.cache.ttl_seconds', 3600);

        $cache = $store ? Cache::store($store) : Cache::store();

        return $cache->remember($key, $ttl, $callback);
    }

    protected function request(string $method, string $resource, array $query = [], ?array $payload = null, ?string $endpointName = null): array
    {
        $query = $this->normalizePaginationParams($query);
        $url = $this->buildUrl($resource);

        $rateLimitKey = 'relograde:'.$this->connection->getKey();
        $maxPerMinute = (int) config('relograde.rate_limit.max_per_minute', 60);
        $this->rateLimiter->throttle($rateLimitKey, $maxPerMinute);

        $startedAt = microtime(true);

        try {
            $response = $this->send($method, $url, $query, $payload);
            $durationMs = (int) round((microtime(true) - $startedAt) * 1000);

            $this->logger->log($this->connection, [
                'method' => strtoupper($method),
                'url' => $url,
                'endpoint_name' => $endpointName,
                'request_headers' => [
                    'Accept' => 'application/json',
                ],
                'request_body' => $payload,
                'response_status' => $response->status(),
                'response_body' => $response->json(),
                'duration_ms' => $durationMs,
            ]);

            if ($response->successful() || $response->status() === 204) {
                return $response->json() ?? [];
            }

            throw RelogradeApiException::fromResponse($response->status(), $response->json());
        } catch (Throwable $exception) {
            $durationMs = (int) round((microtime(true) - $startedAt) * 1000);
            $this->logger->log($this->connection, [
                'method' => strtoupper($method),
                'url' => $url,
                'endpoint_name' => $endpointName,
                'request_headers' => [
                    'Accept' => 'application/json',
                ],
                'request_body' => $payload,
                'response_status' => $exception instanceof RelogradeApiException ? $exception->statusCode() : null,
                'response_body' => $exception instanceof RelogradeApiException ? $exception->payload() : null,
                'duration_ms' => $durationMs,
                'error' => $exception->getMessage(),
            ]);

            if ($exception instanceof RelogradeApiException) {
                throw $exception;
            }

            throw RelogradeApiException::fromResponse(null, null, 'درخواست رلوگرید ناموفق بود.');
        }
    }

    protected function send(string $method, string $url, array $query, ?array $payload): Response
    {
        $pending = $this->pendingRequest();

        $options = [];
        if (! empty($query)) {
            $options['query'] = $query;
        }
        if ($payload !== null) {
            $options['json'] = $payload;
        }

        $retryTimes = (int) config('relograde.http.retry_times', 2);
        $retrySleep = (int) config('relograde.http.retry_sleep_ms', 500);

        $pending = $pending->retry($retryTimes, $retrySleep, function ($exception, $request) {
            if ($exception instanceof \Illuminate\Http\Client\RequestException) {
                $response = $exception->response;

                return $response && in_array($response->status(), [429, 500, 502, 503, 504], true);
            }

            return $exception instanceof \Illuminate\Http\Client\ConnectionException;
        }, false);

        return $pending->send($method, $url, $options);
    }

    protected function pendingRequest(): PendingRequest
    {
        $timeout = (int) config('relograde.http.timeout', 40);

        return Http::withToken($this->connection->api_key)
            ->acceptJson()
            ->timeout($timeout);
    }

    protected function buildUrl(string $resource): string
    {
        $baseUrl = rtrim($this->connection->resolvedBaseUrl(), '/');
        $version = trim($this->connection->resolvedApiVersion(), '/');
        $resource = ltrim($resource, '/');

        return "{$baseUrl}/api/{$version}/{$resource}";
    }

    protected function normalizePaginationParams(array $params): array
    {
        $hasPage = Arr::has($params, 'page');
        $hasLimit = Arr::has($params, 'limit');

        if ($hasPage && ! $hasLimit) {
            $params['limit'] = 20;
        }

        if ($hasLimit && ! $hasPage) {
            $params['page'] = 1;
        }

        return $params;
    }
}
