<?php

namespace Haida\FilamentThreeCx\Services;

use Haida\FilamentThreeCx\Models\ThreeCxInstance;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class ThreeCxHttp
{
    /**
     * @param  array<string, mixed>  $query
     * @param  array<string, mixed>|null  $body
     */
    public function request(
        ThreeCxInstance $instance,
        string $method,
        string $path,
        array $query = [],
        ?array $body = null,
        ?string $accessToken = null,
    ): Response {
        $pending = $this->pendingRequest($instance, $accessToken);
        $url = $this->buildUrl($instance->base_url, $path);

        $options = [];
        if (! empty($query)) {
            $options['query'] = $query;
        }
        if ($body !== null) {
            $options['json'] = $body;
        }

        return $pending->send($method, $url, $options);
    }

    public function pendingRequest(ThreeCxInstance $instance, ?string $accessToken = null): PendingRequest
    {
        $timeout = (int) config('filament-threecx.http.timeout_seconds', 30);
        $retryTimes = (int) config('filament-threecx.http.retry_times', 2);
        $retrySleep = (int) config('filament-threecx.http.retry_sleep_ms', 500);

        $headers = [];
        $correlationId = app()->bound('correlation_id') ? app('correlation_id') : null;
        if (is_string($correlationId) && $correlationId !== '') {
            $headers['X-Correlation-Id'] = $correlationId;
        }

        $userAgent = (string) config('filament-threecx.http.user_agent', 'Haida-ThreeCx/1.0');
        if ($userAgent !== '') {
            $headers['User-Agent'] = $userAgent;
        }

        $pending = Http::withHeaders($headers)
            ->acceptJson()
            ->timeout($timeout)
            ->retry($retryTimes, $retrySleep, function (Throwable $exception): bool {
                if ($exception instanceof RequestException) {
                    $status = $exception->response?->status();

                    return in_array($status, [429, 500, 502, 503, 504], true);
                }

                return $exception instanceof ConnectionException;
            }, false);

        if (! $instance->verify_tls) {
            $pending = $pending->withoutVerifying();
        }

        if ($accessToken) {
            $pending = $pending->withToken($accessToken);
        }

        return $pending;
    }

    public function buildUrl(string $baseUrl, string $path): string
    {
        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        return Str::of($baseUrl)->rtrim('/')->append('/')->append(ltrim($path, '/'))->toString();
    }
}
