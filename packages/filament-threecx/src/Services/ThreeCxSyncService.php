<?php

namespace Haida\FilamentThreeCx\Services;

use Haida\FilamentThreeCx\Clients\XapiClient;
use Haida\FilamentThreeCx\Models\ThreeCxCallLog;
use Haida\FilamentThreeCx\Models\ThreeCxContact;
use Haida\FilamentThreeCx\Models\ThreeCxInstance;
use Haida\FilamentThreeCx\Models\ThreeCxSyncCursor;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class ThreeCxSyncService
{
    public function __construct(
        protected ThreeCxEventDispatcher $events,
    ) {}

    public function syncContacts(ThreeCxInstance $instance): int
    {
        $client = app(XapiClient::class, ['instance' => $instance]);
        $cursor = $this->cursor($instance, 'contacts');

        $count = 0;
        foreach ($this->iterateXapi($client, (string) config('filament-threecx.xapi.contacts_path', '/contacts')) as $item) {
            if (! is_array($item)) {
                continue;
            }

            $externalId = (string) ($item['id'] ?? $item['Id'] ?? $item['external_id'] ?? '');
            if ($externalId === '') {
                $externalId = sha1(json_encode($item));
            }

            $phones = Arr::wrap($item['phones'] ?? $item['phone'] ?? $item['Phone'] ?? null);
            $emails = Arr::wrap($item['emails'] ?? $item['email'] ?? $item['Email'] ?? null);

            $contact = ThreeCxContact::updateOrCreate([
                'tenant_id' => $instance->tenant_id,
                'instance_id' => $instance->getKey(),
                'external_id' => $externalId,
            ], [
                'name' => (string) ($item['name'] ?? $item['Name'] ?? ''),
                'phones' => array_values(array_filter($phones, fn ($value) => $value !== null && $value !== '')),
                'emails' => array_values(array_filter($emails, fn ($value) => $value !== null && $value !== '')),
                'crm_url' => $item['crm_url'] ?? $item['crmUrl'] ?? null,
                'raw_payload' => $this->shouldStoreRaw() ? $item : null,
            ]);

            if ($contact->wasRecentlyCreated) {
                $this->events->dispatchContactCreated($contact);
            }

            $count++;
        }

        $cursor->update([
            'last_synced_at' => now(),
            'cursor' => null,
        ]);

        return $count;
    }

    public function syncCallHistory(ThreeCxInstance $instance): int
    {
        $client = app(XapiClient::class, ['instance' => $instance]);
        $cursor = $this->cursor($instance, 'call_history');

        $count = 0;
        foreach ($this->iterateXapi($client, (string) config('filament-threecx.xapi.call_history_path', '/call-history')) as $item) {
            if (! is_array($item)) {
                continue;
            }

            $payload = $this->mapCallLogPayload($item);

            $log = ThreeCxCallLog::updateOrCreate([
                'tenant_id' => $instance->tenant_id,
                'instance_id' => $instance->getKey(),
                'external_id' => $payload['external_id'],
            ], $payload);

            if ($this->isMissedStatus($log->status) && ($log->wasRecentlyCreated || $log->wasChanged('status'))) {
                $this->events->dispatchMissedCall($log);
            }

            $count++;
        }

        $cursor->update([
            'last_synced_at' => now(),
            'cursor' => null,
        ]);

        return $count;
    }

    public function syncChatHistory(ThreeCxInstance $instance): int
    {
        $client = app(XapiClient::class, ['instance' => $instance]);
        $cursor = $this->cursor($instance, 'chat_history');

        $count = 0;
        foreach ($this->iterateXapi($client, (string) config('filament-threecx.xapi.chat_history_path', '/chat-history')) as $item) {
            if (! is_array($item)) {
                continue;
            }

            $payload = $this->mapCallLogPayload($item, 'chat');

            $log = ThreeCxCallLog::updateOrCreate([
                'tenant_id' => $instance->tenant_id,
                'instance_id' => $instance->getKey(),
                'external_id' => $payload['external_id'],
            ], $payload);

            if ($this->isMissedStatus($log->status) && ($log->wasRecentlyCreated || $log->wasChanged('status'))) {
                $this->events->dispatchMissedCall($log);
            }

            $count++;
        }

        $cursor->update([
            'last_synced_at' => now(),
            'cursor' => null,
        ]);

        return $count;
    }

    protected function cursor(ThreeCxInstance $instance, string $entity): ThreeCxSyncCursor
    {
        return ThreeCxSyncCursor::firstOrCreate([
            'tenant_id' => $instance->tenant_id,
            'instance_id' => $instance->getKey(),
            'entity' => $entity,
        ]);
    }

    /**
     * @return array<int, mixed>
     */
    protected function iterateXapi(XapiClient $client, string $path): array
    {
        $batchSize = (int) config('filament-threecx.sync.batch_size', 100);
        $skip = 0;
        $nextLink = null;
        $items = [];

        while (true) {
            $query = $nextLink ? [] : ['top' => $batchSize, 'skip' => $skip];
            $response = $nextLink
                ? $client->request('GET', $nextLink)
                : $client->request('GET', $path, $query);

            $pageItems = $this->extractItems($response);
            $items = array_merge($items, $pageItems);

            $nextLink = $this->extractNextLink($response);
            if ($nextLink) {
                continue;
            }

            if (count($pageItems) < $batchSize) {
                break;
            }

            $skip += $batchSize;
        }

        return $items;
    }

    /**
     * @param  array<string, mixed>  $response
     * @return array<int, mixed>
     */
    protected function extractItems(array $response): array
    {
        if (isset($response['value']) && is_array($response['value'])) {
            return $response['value'];
        }

        if (isset($response['data']) && is_array($response['data'])) {
            return $response['data'];
        }

        return array_is_list($response) ? $response : [];
    }

    /**
     * @param  array<string, mixed>  $response
     */
    protected function extractNextLink(array $response): ?string
    {
        $candidates = [
            $response['@odata.nextLink'] ?? null,
            $response['odata.nextLink'] ?? null,
            $response['nextLink'] ?? null,
            $response['next'] ?? null,
            data_get($response, 'links.next'),
        ];

        foreach ($candidates as $candidate) {
            if (is_string($candidate) && $candidate !== '') {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    protected function mapCallLogPayload(array $item, ?string $fallbackDirection = null): array
    {
        $from = $item['from_number'] ?? $item['from'] ?? $item['caller'] ?? $item['source'] ?? null;
        $to = $item['to_number'] ?? $item['to'] ?? $item['callee'] ?? $item['destination'] ?? null;
        $started = $item['started_at'] ?? $item['start_time'] ?? $item['startTime'] ?? $item['time'] ?? null;
        $ended = $item['ended_at'] ?? $item['end_time'] ?? $item['endTime'] ?? null;
        $externalId = (string) ($item['external_id'] ?? $item['id'] ?? $item['call_id'] ?? '');

        if ($externalId === '') {
            $externalId = sha1((string) $from.'|'.(string) $to.'|'.(string) $started);
        }

        return [
            'direction' => (string) ($item['direction'] ?? $item['call_direction'] ?? $fallbackDirection ?? ''),
            'from_number' => $from ? (string) $from : null,
            'to_number' => $to ? (string) $to : null,
            'started_at' => $this->parseTimestamp($started),
            'ended_at' => $this->parseTimestamp($ended),
            'duration' => isset($item['duration']) ? (int) $item['duration'] : (isset($item['duration_seconds']) ? (int) $item['duration_seconds'] : null),
            'status' => (string) ($item['status'] ?? $item['result'] ?? ''),
            'external_id' => $externalId,
            'raw_payload' => $this->shouldStoreRaw() ? $item : null,
        ];
    }

    protected function parseTimestamp(mixed $value): ?Carbon
    {
        if (! $value) {
            return null;
        }

        if ($value instanceof Carbon) {
            return $value;
        }

        if (is_numeric($value)) {
            return Carbon::createFromTimestamp((int) $value);
        }

        try {
            return Carbon::parse((string) $value);
        } catch (\Throwable) {
            return null;
        }
    }

    protected function shouldStoreRaw(): bool
    {
        return (bool) config('filament-threecx.sync.store_raw_payload', false);
    }

    protected function isMissedStatus(?string $value): bool
    {
        $value = strtolower((string) $value);

        return in_array($value, ['missed', 'no_answer', 'noanswer', 'unanswered'], true);
    }
}
