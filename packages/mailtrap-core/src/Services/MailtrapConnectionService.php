<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Services;

use Filamat\IamSuite\Support\TenantContext;
use Haida\MailtrapCore\Clients\MailtrapClient;
use Haida\MailtrapCore\Clients\MailtrapSandboxSendClient;
use Haida\MailtrapCore\Clients\MailtrapSendClient;
use Haida\MailtrapCore\Models\MailtrapConnection;
use Haida\MailtrapCore\Support\MailtrapRateLimiter;
use Illuminate\Support\Arr;

class MailtrapConnectionService
{
    public function __construct(
        protected MailtrapRateLimiter $rateLimiter,
    ) {}

    public function resolveConnection(?int $connectionId = null): ?MailtrapConnection
    {
        $tenant = TenantContext::getTenant();
        if (! $tenant) {
            return null;
        }

        if ($connectionId) {
            return MailtrapConnection::query()
                ->where('tenant_id', $tenant->getKey())
                ->where('id', $connectionId)
                ->first();
        }

        return MailtrapConnection::query()
            ->where('tenant_id', $tenant->getKey())
            ->default()
            ->first();
    }

    public function client(MailtrapConnection $connection): MailtrapClient
    {
        return new MailtrapClient($connection, $this->rateLimiter);
    }

    public function sendClient(MailtrapConnection $connection): MailtrapSendClient
    {
        return new MailtrapSendClient($connection, $this->rateLimiter);
    }

    public function sandboxSendClient(MailtrapConnection $connection): MailtrapSandboxSendClient
    {
        return new MailtrapSandboxSendClient($connection, $this->rateLimiter);
    }

    public function testConnection(MailtrapConnection $connection): array
    {
        $accountsResponse = $this->client($connection)->listAccounts();
        $accounts = $accountsResponse['data'] ?? $accountsResponse['accounts'] ?? $accountsResponse;
        $accountIds = collect($accounts)->pluck('id')->filter()->map(fn ($id) => (int) $id)->values()->all();
        $accountId = $connection->account_id ? (int) $connection->account_id : null;
        if (! $accountId || ! in_array($accountId, $accountIds, true)) {
            $accountId = $accountIds[0] ?? null;
        }

        $connection->update([
            'account_id' => $accountId,
            'last_tested_at' => now(),
            'metadata' => array_merge($connection->metadata ?? [], [
                'accounts' => $accounts,
            ]),
        ]);

        return [
            'accounts' => $accounts,
            'account_id' => $accountId,
        ];
    }

    public function resolveAccountId(MailtrapConnection $connection): ?int
    {
        $accountsResponse = $this->client($connection)->listAccounts();
        $accounts = $accountsResponse['data'] ?? $accountsResponse['accounts'] ?? $accountsResponse;
        $accountIds = collect($accounts)->pluck('id')->filter()->map(fn ($id) => (int) $id)->values()->all();
        $accountId = $connection->account_id ? (int) $connection->account_id : null;
        if ($accountId && in_array($accountId, $accountIds, true)) {
            return $accountId;
        }

        $accountId = $accountIds[0] ?? null;
        if ($accountId) {
            $connection->update(['account_id' => (int) $accountId]);
        }

        return $accountId ?: null;
    }
}
