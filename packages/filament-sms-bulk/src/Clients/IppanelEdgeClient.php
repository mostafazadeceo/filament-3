<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Clients;

use Haida\SmsBulk\Clients\Dto\EdgeRequestContext;
use Haida\SmsBulk\Clients\Dto\EdgeResponseDto;
use Haida\SmsBulk\Clients\Exceptions\IppanelAuthException;
use Haida\SmsBulk\Clients\Exceptions\IppanelRateLimitException;
use Haida\SmsBulk\Clients\Exceptions\IppanelServerException;
use Haida\SmsBulk\Clients\Exceptions\IppanelTransportException;
use Haida\SmsBulk\Clients\Exceptions\IppanelValidationException;
use Haida\SmsBulk\Contracts\ProviderClientInterface;
use Haida\SmsBulk\Models\SmsBulkProviderConnection;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class IppanelEdgeClient implements ProviderClientInterface
{
    public function __construct(
        protected HttpFactory $http,
        protected ?SmsBulkProviderConnection $connection = null,
    ) {}

    public function myCredit(): array { return $this->request('GET', '/api/payment/my-credit'); }

    public function sendWebservice(array $payload): array { return $this->request('POST', '/api/send/webservice', payload: $payload); }
    public function sendPeerToPeer(array $payload): array { return $this->request('POST', '/api/send/peer-to-peer', payload: $payload); }
    public function sendPeerToPeerFile(array $payload): array { return $this->request('POST', '/api/send/peer-to-peer-file', payload: $payload); }
    public function sendPostalCode(array $payload): array { return $this->request('POST', '/api/send/postalcode', payload: $payload); }
    public function sendCountryProvince(array $payload): array { return $this->request('POST', '/api/send/country/province', payload: $payload); }
    public function sendCountryCounty(array $payload): array { return $this->request('POST', '/api/send/country/county', payload: $payload); }
    public function sendCountryCity(array $payload): array { return $this->request('POST', '/api/send/country/city', payload: $payload); }
    public function sendCountryCount(array $payload): array { return $this->request('POST', '/api/send/country/count', payload: $payload); }
    public function sendCountryGender(array $payload): array { return $this->request('POST', '/api/send/country/gender', payload: $payload); }
    public function sendCountryV2Province(array $payload): array { return $this->request('POST', '/api/send/countryV2/province', payload: $payload); }
    public function sendCountryV2County(array $payload): array { return $this->request('POST', '/api/send/countryV2/county', payload: $payload); }
    public function sendCountryV2City(array $payload): array { return $this->request('POST', '/api/send/countryV2/city', payload: $payload); }
    public function sendCountryV2Count(array $payload): array { return $this->request('POST', '/api/send/countryV2/count', payload: $payload); }
    public function sendJobCategories(): array { return $this->request('GET', '/api/send/jobs/categories'); }
    public function sendJobSubCategory(array $payload): array { return $this->request('POST', '/api/send/jobs/sub-category', payload: $payload); }
    public function sendJobCount(array $payload): array { return $this->request('POST', '/api/send/jobs/count', payload: $payload); }
    public function sendKeyword(array $payload): array { return $this->request('POST', '/api/send/keyword', payload: $payload); }
    public function sendKeywordPhonebook(array $payload): array { return $this->request('POST', '/api/send/keyword-phonebook', payload: $payload); }
    public function sendPhonebook(array $payload): array { return $this->request('POST', '/api/send/phonebook', payload: $payload); }
    public function sendPattern(array $payload): array { return $this->request('POST', '/api/send/pattern', payload: $payload); }
    public function sendFile(array $payload): array { return $this->request('POST', '/api/send/file', payload: $payload); }
    public function sendVotp(array $payload): array { return $this->request('POST', '/api/send/votp', payload: $payload); }
    public function sendUrl(array $payload): array { return $this->request('POST', '/api/send/url', payload: $payload); }
    public function calculatePrice(array $payload): array { return $this->request('POST', '/api/send/calculate-price', payload: $payload); }
    public function cancelScheduled(array $payload): array { return $this->request('POST', '/api/send/cancel-scheduled', payload: $payload); }

    public function reportOutbox(array $query = []): array { return $this->request('GET', '/api/report/outbox-report', query: $query); }
    public function reportOutboxById(string $id): array { return $this->request('GET', "/api/report/outbox-report-id/{$id}"); }
    public function reportInbox(array $query = []): array { return $this->request('GET', '/api/report/inbox-report', query: $query); }
    public function reportBulkStats(string $bulkId): array { return $this->request('GET', "/api/report/bulk-stats/{$bulkId}"); }
    public function reportBulkRecipients(string $bulkId, array $query = []): array { return $this->request('GET', "/api/report/bulk-recipient/{$bulkId}", query: $query); }

    public function phonebookList(): array { return $this->request('GET', '/api/phonebook/phonebook/phonebook-list'); }
    public function phonebookStore(array $payload): array { return $this->request('POST', '/api/phonebook/phonebook/store-phonebook', payload: $payload); }
    public function phonebookUpdate(string $phonebookId, array $payload): array { return $this->request('POST', "/api/phonebook/phonebook/update-phonebook/{$phonebookId}", payload: $payload); }
    public function phonebookDelete(string $phonebookId): array { return $this->request('POST', "/api/phonebook/phonebook/delete-phonebook/{$phonebookId}"); }

    public function optionList(string $phonebookId): array { return $this->request('GET', '/api/phonebook/option/list-option', ['phonebook_id' => $phonebookId]); }
    public function optionStore(string $phonebookId, array $payload): array { return $this->request('POST', '/api/phonebook/option/store-option', payload: array_merge($payload, ['phonebook_id' => $phonebookId])); }
    public function optionUpdate(string $optionId, array $payload): array { return $this->request('POST', "/api/phonebook/option/update-option/{$optionId}", payload: $payload); }
    public function optionDelete(string $optionId): array { return $this->request('POST', "/api/phonebook/option/delete-option/{$optionId}"); }

    public function numberList(string $phonebookId, array $query = []): array { return $this->request('GET', '/api/phonebook/number/list-number', query: array_merge($query, ['phonebook_id' => $phonebookId])); }
    public function numberShow(string $numberId): array { return $this->request('GET', "/api/phonebook/number/show-number/{$numberId}"); }
    public function numberStore(string $phonebookId, array $payload): array { return $this->request('POST', '/api/phonebook/number/store-number', payload: array_merge($payload, ['phonebook_id' => $phonebookId])); }
    public function numberUpdate(string $numberId, array $payload): array { return $this->request('POST', "/api/phonebook/number/update-number/{$numberId}", payload: $payload); }
    public function numberDelete(string $numberId): array { return $this->request('POST', "/api/phonebook/number/delete-number/{$numberId}"); }
    public function numberImport(string $phonebookId, array $payload): array { return $this->request('POST', '/api/phonebook/number/import-number', payload: array_merge($payload, ['phonebook_id' => $phonebookId])); }
    public function numberSampleImport(): array { return $this->request('GET', '/api/phonebook/number/sample-import-number'); }
    public function numberExportContacts(string $phonebookId): array { return $this->request('GET', '/api/phonebook/number/export-number-contacts', ['phonebook_id' => $phonebookId]); }
    public function numberExportMembers(string $phonebookId): array { return $this->request('GET', '/api/phonebook/number/export-number-members', ['phonebook_id' => $phonebookId]); }

    public function patternList(): array { return $this->request('GET', '/api/pattern/list-pattern'); }
    public function patternByCode(string $patternCode): array { return $this->request('GET', "/api/pattern/pattern-by-code/{$patternCode}"); }
    public function patternCreate(array $payload): array { return $this->request('POST', '/api/pattern/create-pattern', payload: $payload); }
    public function patternUpdate(string $patternCode, array $payload): array { return $this->request('POST', "/api/pattern/update-pattern/{$patternCode}", payload: $payload); }
    public function patternDelete(string $patternCode): array { return $this->request('POST', "/api/pattern/delete-pattern/{$patternCode}"); }

    public function draftGroupList(): array { return $this->request('GET', '/api/draft/list-draft-group'); }
    public function draftGroupCreate(array $payload): array { return $this->request('POST', '/api/draft/create-draft-group', payload: $payload); }
    public function draftGroupUpdate(string $groupId, array $payload): array { return $this->request('POST', "/api/draft/update-draft-group/{$groupId}", payload: $payload); }
    public function draftGroupDelete(string $groupId): array { return $this->request('POST', "/api/draft/delete-draft-group/{$groupId}"); }

    public function draftList(string $groupId): array { return $this->request('GET', '/api/draft/list-draft', ['draft_group_id' => $groupId]); }
    public function draftCreate(string $groupId, array $payload): array { return $this->request('POST', '/api/draft/create-draft', payload: array_merge($payload, ['draft_group_id' => $groupId])); }
    public function draftUpdate(string $draftId, array $payload): array { return $this->request('POST', "/api/draft/update-draft/{$draftId}", payload: $payload); }
    public function draftDelete(string $draftId): array { return $this->request('POST', "/api/draft/delete-draft/{$draftId}"); }

    public function userList(array $query = []): array { return $this->request('GET', '/api/user/list-users', query: $query); }
    public function userShow(string $userId): array { return $this->request('GET', "/api/user/show-user/{$userId}"); }
    public function userRegister(array $payload): array { return $this->request('POST', '/api/user/register-user', payload: $payload); }
    public function userUpdate(string $userId, array $payload): array { return $this->request('POST', "/api/user/update-user/{$userId}", payload: $payload); }
    public function userTariff(string $userId): array { return $this->request('GET', "/api/user/user-tariff/{$userId}"); }
    public function userExists(string $mobile): array { return $this->request('GET', '/api/user/check-exist', ['mobile' => $mobile]); }
    public function userParentsTree(string $userId): array { return $this->request('GET', "/api/user/parents-tree/{$userId}"); }

    public function packageList(): array { return $this->request('GET', '/api/package/list-packages'); }

    public function numberPoolList(): array { return $this->request('GET', '/api/numbers/list-numbers'); }
    public function numberAssign(array $payload): array { return $this->request('POST', '/api/numbers/assign-number', payload: $payload); }
    public function numberUnassign(array $payload): array { return $this->request('POST', '/api/numbers/unassign-number', payload: $payload); }

    public function ticketList(): array { return $this->request('GET', '/api/ticket/list-ticket'); }
    public function ticketShow(string $ticketId): array { return $this->request('GET', "/api/ticket/ticket-id/{$ticketId}"); }
    public function ticketCreate(array $payload): array { return $this->request('POST', '/api/ticket/create-ticket', payload: $payload); }
    public function ticketReply(string $ticketId, array $payload): array { return $this->request('POST', "/api/ticket/reply-ticket/{$ticketId}", payload: $payload); }

    public function request(string $method, string $path, array $query = [], ?array $payload = null): array
    {
        $context = new EdgeRequestContext(
            correlationId: (string) Str::uuid(),
            tenantId: $this->connection?->tenant_id,
            providerConnectionId: $this->connection?->getKey(),
        );

        try {
            $pending = $this->prepareRequest($context);
            $response = match (strtoupper($method)) {
                'GET' => $pending->get($path, $query),
                'POST' => $pending->post($path, $payload ?? []),
                'PUT' => $pending->put($path, $payload ?? []),
                'PATCH' => $pending->patch($path, $payload ?? []),
                'DELETE' => $pending->delete($path, $payload ?? []),
                default => throw new IppanelValidationException('Unsupported HTTP method: '.$method),
            };
        } catch (ConnectionException $exception) {
            throw new IppanelTransportException(
                message: 'Edge API transport failure: '.$exception->getMessage(),
                payload: ['path' => $path, 'correlation_id' => $context->correlationId],
            );
        }

        $normalized = $this->normalizeResponse($response, $context);

        Log::info('sms-bulk.ippanel.request', [
            'correlation_id' => $context->correlationId,
            'tenant_id' => $context->tenantId,
            'provider_connection_id' => $context->providerConnectionId,
            'method' => strtoupper($method),
            'path' => $path,
            'status_code' => $response->status(),
        ]);

        return $normalized->toArray();
    }

    protected function prepareRequest(EdgeRequestContext $context): PendingRequest
    {
        $baseUrl = $this->connection?->base_url_override ?: (string) config('filament-sms-bulk.provider.ippanel_edge.base_url');
        $token = $this->connection?->encrypted_token ?: (string) config('filament-sms-bulk.provider.ippanel_edge.token');

        return $this->http
            ->baseUrl(rtrim($baseUrl, '/'))
            ->acceptJson()
            ->asJson()
            ->withHeaders([
                'Authorization' => $token,
                'X-Correlation-Id' => $context->correlationId,
            ])
            ->timeout((int) config('filament-sms-bulk.provider.ippanel_edge.timeout_seconds', 15))
            ->retry(
                times: (int) config('filament-sms-bulk.provider.ippanel_edge.retry_times', 3),
                sleepMilliseconds: (int) config('filament-sms-bulk.provider.ippanel_edge.retry_sleep_ms', 250),
                throw: false,
            );
    }

    protected function normalizeResponse(Response $response, EdgeRequestContext $context): EdgeResponseDto
    {
        /** @var array<string, mixed> $body */
        $body = $response->json() ?? [];

        if ($response->successful()) {
            return new EdgeResponseDto(
                data: (array) ($body['data'] ?? []),
                meta: (array) ($body['meta'] ?? []),
                statusCode: $response->status(),
                correlationId: $context->correlationId,
            );
        }

        $message = (string) (($body['meta']['message'] ?? null) ?: $response->body());
        $payload = [
            'response' => $body,
            'status' => $response->status(),
            'correlation_id' => $context->correlationId,
        ];

        if (in_array($response->status(), [401, 403], true)) {
            throw new IppanelAuthException($message, $response->status(), $payload);
        }

        if ($response->status() === 429) {
            throw new IppanelRateLimitException($message, $response->status(), $payload);
        }

        if ($response->status() >= 500) {
            throw new IppanelServerException($message, $response->status(), $payload);
        }

        throw new IppanelValidationException($message, $response->status(), $payload);
    }
}
