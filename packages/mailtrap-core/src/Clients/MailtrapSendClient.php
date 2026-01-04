<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Clients;

use Haida\MailtrapCore\Exceptions\MailtrapApiException;
use Haida\MailtrapCore\Models\MailtrapConnection;
use Haida\MailtrapCore\Support\MailtrapRateLimiter;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class MailtrapSendClient
{
    public function __construct(
        protected MailtrapConnection $connection,
        protected MailtrapRateLimiter $rateLimiter,
    ) {}

    public function sendEmail(array $payload): array
    {
        $response = $this->request('POST', 'send', [], $payload, 'sendEmail');

        if ($response->successful() || $response->status() === 202) {
            return $response->json() ?? [];
        }

        throw MailtrapApiException::fromResponse($response->status(), $response->json());
    }

    protected function request(string $method, string $resource, array $query = [], ?array $payload = null, ?string $endpointName = null): Response
    {
        $url = $this->buildUrl($resource);
        $rateConfig = config('mailtrap-core.rate_limit', []);
        $maxRequests = (int) ($rateConfig['max_requests'] ?? 10);
        $perSeconds = (int) ($rateConfig['per_seconds'] ?? 1);
        $rateLimitKey = 'mailtrap-send:' . $this->connection->getKey() . ':' . $perSeconds;

        $this->rateLimiter->throttle($rateLimitKey, $maxRequests);

        $startedAt = microtime(true);

        try {
            $response = $this->send($method, $url, $query, $payload);
            $durationMs = (int) round((microtime(true) - $startedAt) * 1000);

            $this->logRequest($method, $url, $endpointName, $payload, $response, $durationMs, null);

            return $response;
        } catch (Throwable $exception) {
            $durationMs = (int) round((microtime(true) - $startedAt) * 1000);
            $status = $exception instanceof MailtrapApiException ? $exception->statusCode() : null;
            $payload = $exception instanceof MailtrapApiException ? $exception->payload() : null;

            $this->logRequest($method, $url, $endpointName, $payload, null, $durationMs, $exception->getMessage(), $status);

            if ($exception instanceof MailtrapApiException) {
                throw $exception;
            }

            throw MailtrapApiException::fromResponse(null, null, 'ارسال ایمیل از طریق Mailtrap ناموفق بود.');
        }
    }

    protected function send(string $method, string $url, array $query = [], ?array $payload = null): Response
    {
        $timeout = (int) config('mailtrap-core.http.timeout_seconds', 30);
        $retryTimes = (int) config('mailtrap-core.http.retry_times', 2);
        $retrySleep = (int) config('mailtrap-core.http.retry_sleep_ms', 500);

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
        $headers = [];
        $correlationId = app()->bound('correlation_id') ? app('correlation_id') : null;
        if (is_string($correlationId) && $correlationId !== '') {
            $headers['X-Correlation-Id'] = $correlationId;
        }

        $token = $this->connection->send_api_token ?: $this->connection->api_token;

        return Http::withHeaders($headers)
            ->withToken((string) $token)
            ->acceptJson();
    }

    protected function buildUrl(string $resource): string
    {
        $base = (string) config('mailtrap-core.send_base_url', 'https://send.api.mailtrap.io/api');

        return Str::of($base)->rtrim('/')->append('/')->append(ltrim($resource, '/'))->toString();
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
        if (! (bool) config('mailtrap-core.logging.enabled', true)) {
            return;
        }

        logger()->info('mailtrap.send', [
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
