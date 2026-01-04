<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Clients;

use Haida\MailtrapCore\Exceptions\MailtrapApiException;
use Haida\MailtrapCore\Models\MailtrapConnection;
use Haida\MailtrapCore\Support\MailtrapFakeResponse;
use Haida\MailtrapCore\Support\MailtrapRateLimiter;
use GuzzleHttp\Psr7\Response as PsrResponse;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Client\Response as HttpResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class MailtrapClient
{
    public function __construct(
        protected MailtrapConnection $connection,
        protected MailtrapRateLimiter $rateLimiter,
    ) {}

    public function listAccounts(): array
    {
        return $this->requestJson('GET', 'accounts', [], null, 'listAccounts');
    }

    public function listInboxes(int $accountId): array
    {
        return $this->requestJson('GET', "accounts/{$accountId}/inboxes", [], null, 'listInboxes');
    }

    public function createInbox(int $accountId, array $payload): array
    {
        $projectId = (int) ($payload['project_id'] ?? 0);
        $inboxData = $this->buildInboxData($payload);
        $inboxPayload = ['inbox' => $inboxData];

        if ($projectId > 0) {
            try {
                return $this->requestJson(
                    'POST',
                    "accounts/{$accountId}/projects/{$projectId}/inboxes",
                    [],
                    $inboxPayload,
                    'createInboxProject',
                );
            } catch (MailtrapApiException $exception) {
                if ($exception->statusCode() !== 404) {
                    throw $exception;
                }
            }
        }

        try {
            return $this->requestJson('POST', "accounts/{$accountId}/inboxes", [], $inboxPayload, 'createInbox');
        } catch (MailtrapApiException $exception) {
            if ($exception->statusCode() === 422 && ! empty($inboxData)) {
                return $this->requestJson('POST', "accounts/{$accountId}/inboxes", [], $inboxData, 'createInboxLegacy');
            }

            throw $exception;
        }
    }

    public function updateInbox(int $accountId, int $inboxId, array $payload): array
    {
        $inboxData = $this->buildInboxData($payload);

        try {
            return $this->requestJson(
                'PATCH',
                "accounts/{$accountId}/inboxes/{$inboxId}",
                [],
                ['inbox' => $inboxData],
                'updateInbox'
            );
        } catch (MailtrapApiException $exception) {
            if ($exception->statusCode() === 422 && ! empty($inboxData)) {
                return $this->requestJson(
                    'PATCH',
                    "accounts/{$accountId}/inboxes/{$inboxId}",
                    [],
                    $inboxData,
                    'updateInboxLegacy'
                );
            }

            throw $exception;
        }
    }

    public function deleteInbox(int $accountId, int $inboxId): array
    {
        return $this->requestJson('DELETE', "accounts/{$accountId}/inboxes/{$inboxId}", [], null, 'deleteInbox');
    }

    /**
     * @param array<string, mixed> $payload
     */
    protected function buildInboxPayload(array $payload): array
    {
        return ['inbox' => $this->buildInboxData($payload)];
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, string>
     */
    protected function buildInboxData(array $payload): array
    {
        return array_filter([
            'name' => $payload['name'] ?? null,
            'email_username' => $payload['email_username'] ?? null,
        ], fn ($value) => $value !== null && $value !== '');
    }

    public function findInbox(int $accountId, int $inboxId): array
    {
        return $this->requestJson('GET', "accounts/{$accountId}/inboxes/{$inboxId}", [], null, 'findInbox');
    }

    public function listMessages(int $accountId, int $inboxId, array $params = []): array
    {
        return $this->requestJson('GET', "accounts/{$accountId}/inboxes/{$inboxId}/messages", $params, null, 'listMessages');
    }

    public function findMessage(int $accountId, int $inboxId, int $messageId): array
    {
        return $this->requestJson('GET', "accounts/{$accountId}/inboxes/{$inboxId}/messages/{$messageId}", [], null, 'findMessage');
    }

    public function listMessageAttachments(int $accountId, int $inboxId, int $messageId): array
    {
        return $this->requestJson('GET', "accounts/{$accountId}/inboxes/{$inboxId}/messages/{$messageId}/attachments", [], null, 'listAttachments');
    }

    public function downloadAttachment(int $accountId, int $inboxId, int $messageId, int $attachmentId): string
    {
        return $this->requestRaw('GET', "accounts/{$accountId}/inboxes/{$inboxId}/messages/{$messageId}/attachments/{$attachmentId}", [], null, 'downloadAttachment');
    }

    public function getMessageBody(int $accountId, int $inboxId, int $messageId, string $format = 'html'): string
    {
        $format = strtolower($format) === 'txt' ? 'txt' : 'html';

        return $this->requestRaw('GET', "accounts/{$accountId}/inboxes/{$inboxId}/messages/{$messageId}/body.{$format}", [], null, 'getMessageBody');
    }

    public function listSendingDomains(int $accountId): array
    {
        return $this->requestJson('GET', "accounts/{$accountId}/sending_domains", [], null, 'listSendingDomains');
    }

    public function createSendingDomain(int $accountId, array $payload): array
    {
        return $this->requestJson('POST', "accounts/{$accountId}/sending_domains", [], $payload, 'createSendingDomain');
    }

    public function updateSendingDomain(int $accountId, int $domainId, array $payload): array
    {
        return $this->requestJson('PATCH', "accounts/{$accountId}/sending_domains/{$domainId}", [], $payload, 'updateSendingDomain');
    }

    public function deleteSendingDomain(int $accountId, int $domainId): array
    {
        return $this->requestJson('DELETE', "accounts/{$accountId}/sending_domains/{$domainId}", [], null, 'deleteSendingDomain');
    }

    protected function requestJson(string $method, string $resource, array $query = [], ?array $payload = null, ?string $endpointName = null): array
    {
        $response = $this->request($method, $resource, $query, $payload, $endpointName);

        if ($response->successful() || $response->status() === 204) {
            return $response->json() ?? [];
        }

        throw MailtrapApiException::fromResponse($response->status(), $response->json());
    }

    protected function requestRaw(string $method, string $resource, array $query = [], ?array $payload = null, ?string $endpointName = null): string
    {
        $response = $this->request($method, $resource, $query, $payload, $endpointName);

        if ($response->successful() || $response->status() === 204) {
            return (string) $response->body();
        }

        throw MailtrapApiException::fromResponse($response->status(), $response->json());
    }

    protected function request(string $method, string $resource, array $query = [], ?array $payload = null, ?string $endpointName = null): Response
    {
        if ((bool) config('mailtrap-core.fake')) {
            $fake = MailtrapFakeResponse::handle($method, $resource, $query, $payload);
            $body = $fake['body'];
            $content = is_string($body) ? $body : json_encode($body, JSON_UNESCAPED_UNICODE);
            $headers = is_string($body) ? [] : ['Content-Type' => 'application/json'];

            return new HttpResponse(new PsrResponse($fake['status'], $headers, $content));
        }

        $url = $this->buildUrl($resource);
        $rateConfig = config('mailtrap-core.rate_limit', []);
        $maxRequests = (int) ($rateConfig['max_requests'] ?? 10);
        $perSeconds = (int) ($rateConfig['per_seconds'] ?? 1);
        $rateLimitKey = 'mailtrap:' . $this->connection->getKey() . ':' . $perSeconds;

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

            throw MailtrapApiException::fromResponse(null, null, 'ارتباط با Mailtrap ناموفق بود.');
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

        return Http::withHeaders($headers)
            ->withToken((string) $this->connection->api_token)
            ->acceptJson();
    }

    protected function buildUrl(string $resource): string
    {
        $base = (string) config('mailtrap-core.base_url', 'https://mailtrap.io/api');

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

        logger()->info('mailtrap.http', [
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
