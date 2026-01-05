<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Services;

use Haida\MailtrapCore\Exceptions\MailtrapApiException;
use Haida\MailtrapCore\Models\MailtrapConnection;
use Haida\MailtrapCore\Models\MailtrapInbox;

class MailtrapInboxService
{
    public function __construct(
        protected MailtrapConnectionService $connections,
    ) {}

    public function sync(MailtrapConnection $connection, bool $force = false): int
    {
        $lastSync = $connection->metadata['inbox_last_sync_at'] ?? null;
        if (! $force && $lastSync) {
            $minSeconds = (int) config('mailtrap-core.sync.min_seconds', 300);
            if ($minSeconds > 0 && now()->diffInSeconds($lastSync) < $minSeconds) {
                return 0;
            }
        }

        $accountId = $this->connections->resolveAccountId($connection);
        if (! $accountId) {
            return 0;
        }

        $client = $this->connections->client($connection);
        $inboxesResponse = $client->listInboxes($accountId);
        $inboxes = $inboxesResponse['data'] ?? $inboxesResponse['inboxes'] ?? $inboxesResponse;

        $remoteIds = [];
        $defaultProjectId = null;
        $count = 0;
        foreach ($inboxes as $inbox) {
            $remoteId = (int) ($inbox['id'] ?? 0);
            if ($remoteId > 0) {
                $remoteIds[] = $remoteId;
            }

            if ($defaultProjectId === null && ! empty($inbox['project_id'])) {
                $defaultProjectId = (int) $inbox['project_id'];
            }

            MailtrapInbox::query()->updateOrCreate([
                'tenant_id' => $connection->tenant_id,
                'connection_id' => $connection->getKey(),
                'inbox_id' => (int) ($inbox['id'] ?? 0),
            ], [
                'name' => (string) ($inbox['name'] ?? 'Inbox'),
                'status' => $inbox['status'] ?? null,
                'username' => $inbox['username'] ?? null,
                'email_domain' => $inbox['email_domain'] ?? null,
                'api_domain' => $inbox['api_domain'] ?? null,
                'smtp_ports' => $inbox['smtp_ports'] ?? null,
                'messages_count' => (int) ($inbox['emails_count'] ?? $inbox['messages_count'] ?? 0),
                'unread_count' => (int) ($inbox['emails_unread_count'] ?? $inbox['unread_count'] ?? 0),
                'last_message_sent_at' => $inbox['last_message_sent_at'] ?? null,
                'metadata' => $inbox,
                'synced_at' => now(),
            ]);
            $count++;
        }

        if (! empty($remoteIds)) {
            MailtrapInbox::query()
                ->where('tenant_id', $connection->tenant_id)
                ->where('connection_id', $connection->getKey())
                ->whereNotIn('inbox_id', $remoteIds)
                ->delete();
        }

        $metadata = array_merge($connection->metadata ?? [], [
            'inboxes_total' => count($inboxes),
            'inbox_last_sync_at' => now(),
        ]);

        if ($defaultProjectId) {
            $metadata['default_project_id'] = $defaultProjectId;
        }

        $connection->update([
            'last_sync_at' => now(),
            'metadata' => $metadata,
        ]);

        return $count;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function create(MailtrapConnection $connection, array $payload): MailtrapInbox
    {
        $accountId = $this->connections->resolveAccountId($connection);
        if (! $accountId) {
            throw new \RuntimeException('اکانت معتبر برای ایجاد Inbox پیدا نشد.');
        }

        $client = $this->connections->client($connection);
        $payload = array_filter($payload, fn ($value) => $value !== null && $value !== '');

        if (isset($payload['project_id'])) {
            $payload['project_id'] = (int) $payload['project_id'];
            if ($payload['project_id'] <= 0) {
                unset($payload['project_id']);
            }
        }

        if (empty($payload['project_id'])) {
            $projectId = $this->resolveProjectId($connection, $accountId);
            if ($projectId) {
                $payload['project_id'] = $projectId;
            }
        }

        try {
            $response = $client->createInbox($accountId, $payload);
        } catch (MailtrapApiException $exception) {
            if ($exception->statusCode() === 404 && empty($payload['project_id'])) {
                throw new \RuntimeException('برای ساخت Inbox باید شناسه پروژه (project_id) مشخص شود.');
            }

            throw $exception;
        }
        $inbox = $response['data'] ?? $response;

        return MailtrapInbox::query()->create([
            'tenant_id' => $connection->tenant_id,
            'connection_id' => $connection->getKey(),
            'inbox_id' => (int) ($inbox['id'] ?? 0),
            'name' => (string) ($inbox['name'] ?? ($payload['name'] ?? 'Inbox')),
            'status' => $inbox['status'] ?? ($payload['status'] ?? null),
            'username' => $inbox['username'] ?? null,
            'email_domain' => $inbox['email_domain'] ?? null,
            'api_domain' => $inbox['api_domain'] ?? null,
            'smtp_ports' => $inbox['smtp_ports'] ?? null,
            'messages_count' => (int) ($inbox['emails_count'] ?? $inbox['messages_count'] ?? 0),
            'unread_count' => (int) ($inbox['emails_unread_count'] ?? $inbox['unread_count'] ?? 0),
            'last_message_sent_at' => $inbox['last_message_sent_at'] ?? null,
            'metadata' => $inbox,
            'synced_at' => now(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function update(MailtrapConnection $connection, MailtrapInbox $inbox, array $payload): MailtrapInbox
    {
        $accountId = $this->connections->resolveAccountId($connection);
        if (! $accountId) {
            throw new \RuntimeException('اکانت معتبر برای بروزرسانی Inbox پیدا نشد.');
        }

        $client = $this->connections->client($connection);
        $response = $client->updateInbox($accountId, (int) $inbox->inbox_id, $payload);
        $remote = $response['data'] ?? $response;

        $inbox->update([
            'name' => (string) ($remote['name'] ?? ($payload['name'] ?? $inbox->name)),
            'status' => $remote['status'] ?? ($payload['status'] ?? $inbox->status),
            'metadata' => $remote ?: $inbox->metadata,
            'synced_at' => now(),
        ]);

        return $inbox->refresh();
    }

    public function delete(MailtrapConnection $connection, MailtrapInbox $inbox): void
    {
        $accountId = $this->connections->resolveAccountId($connection);
        if (! $accountId) {
            throw new \RuntimeException('اکانت معتبر برای حذف Inbox پیدا نشد.');
        }

        $client = $this->connections->client($connection);
        $client->deleteInbox($accountId, (int) $inbox->inbox_id);

        $inbox->delete();
    }

    protected function resolveProjectId(MailtrapConnection $connection, ?int $accountId = null): ?int
    {
        $metadata = $connection->metadata ?? [];
        $cachedProjectId = $metadata['default_project_id'] ?? null;
        if ($cachedProjectId) {
            return (int) $cachedProjectId;
        }

        $accountId = $accountId ?: $this->connections->resolveAccountId($connection);
        if (! $accountId) {
            return null;
        }

        $client = $this->connections->client($connection);
        $inboxesResponse = $client->listInboxes($accountId);
        $inboxes = $inboxesResponse['data'] ?? $inboxesResponse['inboxes'] ?? $inboxesResponse;

        foreach ($inboxes as $inbox) {
            if (empty($inbox['project_id'])) {
                continue;
            }

            $projectId = (int) $inbox['project_id'];
            if ($projectId > 0) {
                $connection->update([
                    'metadata' => array_merge($metadata, [
                        'default_project_id' => $projectId,
                    ]),
                ]);

                return $projectId;
            }
        }

        return null;
    }
}
