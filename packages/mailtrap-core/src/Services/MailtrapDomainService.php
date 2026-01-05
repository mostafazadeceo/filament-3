<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Services;

use Haida\MailtrapCore\Models\MailtrapConnection;
use Haida\MailtrapCore\Models\MailtrapSendingDomain;

class MailtrapDomainService
{
    public function __construct(
        protected MailtrapConnectionService $connections,
    ) {}

    public function sync(MailtrapConnection $connection, bool $force = false): int
    {
        $lastSync = $connection->metadata['domain_last_sync_at'] ?? null;
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
        $response = $client->listSendingDomains($accountId);
        $domains = $response['data'] ?? $response;

        $count = 0;
        foreach ($domains as $domain) {
            MailtrapSendingDomain::query()->updateOrCreate([
                'tenant_id' => $connection->tenant_id,
                'connection_id' => $connection->getKey(),
                'domain_id' => (int) ($domain['id'] ?? 0),
            ], [
                'domain_name' => (string) ($domain['domain_name'] ?? ''),
                'dns_verified' => (bool) ($domain['dns_verified'] ?? false),
                'dns_verified_at' => $domain['dns_verified_at'] ?? null,
                'compliance_status' => $domain['compliance_status'] ?? null,
                'demo' => (bool) ($domain['demo'] ?? false),
                'dns_records' => $domain['dns_records'] ?? null,
                'settings' => [
                    'open_tracking_enabled' => $domain['open_tracking_enabled'] ?? null,
                    'click_tracking_enabled' => $domain['click_tracking_enabled'] ?? null,
                    'auto_unsubscribe_link_enabled' => $domain['auto_unsubscribe_link_enabled'] ?? null,
                    'custom_domain_tracking_enabled' => $domain['custom_domain_tracking_enabled'] ?? null,
                    'health_alerts_enabled' => $domain['health_alerts_enabled'] ?? null,
                    'critical_alerts_enabled' => $domain['critical_alerts_enabled'] ?? null,
                    'alert_recipient_email' => $domain['alert_recipient_email'] ?? null,
                ],
                'metadata' => $domain,
                'synced_at' => now(),
            ]);
            $count++;
        }

        $connection->update([
            'last_sync_at' => now(),
            'metadata' => array_merge($connection->metadata ?? [], [
                'domains_total' => count($domains),
                'domain_last_sync_at' => now(),
            ]),
        ]);

        return $count;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function create(MailtrapConnection $connection, array $payload): MailtrapSendingDomain
    {
        $accountId = $this->connections->resolveAccountId($connection);
        if (! $accountId) {
            throw new \RuntimeException('اکانت معتبر برای ایجاد دامنه پیدا نشد.');
        }

        $client = $this->connections->client($connection);
        $response = $client->createSendingDomain($accountId, $payload);
        $domain = $response['data'] ?? $response;

        return MailtrapSendingDomain::query()->create([
            'tenant_id' => $connection->tenant_id,
            'connection_id' => $connection->getKey(),
            'domain_id' => (int) ($domain['id'] ?? 0),
            'domain_name' => (string) ($domain['domain_name'] ?? ($payload['domain_name'] ?? '')),
            'dns_verified' => (bool) ($domain['dns_verified'] ?? false),
            'dns_verified_at' => $domain['dns_verified_at'] ?? null,
            'compliance_status' => $domain['compliance_status'] ?? null,
            'demo' => (bool) ($domain['demo'] ?? false),
            'dns_records' => $domain['dns_records'] ?? null,
            'settings' => [
                'open_tracking_enabled' => $domain['open_tracking_enabled'] ?? null,
                'click_tracking_enabled' => $domain['click_tracking_enabled'] ?? null,
                'auto_unsubscribe_link_enabled' => $domain['auto_unsubscribe_link_enabled'] ?? null,
                'custom_domain_tracking_enabled' => $domain['custom_domain_tracking_enabled'] ?? null,
                'health_alerts_enabled' => $domain['health_alerts_enabled'] ?? null,
                'critical_alerts_enabled' => $domain['critical_alerts_enabled'] ?? null,
                'alert_recipient_email' => $domain['alert_recipient_email'] ?? null,
            ],
            'metadata' => $domain,
            'synced_at' => now(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function update(MailtrapConnection $connection, MailtrapSendingDomain $domain, array $payload): MailtrapSendingDomain
    {
        $accountId = $this->connections->resolveAccountId($connection);
        if (! $accountId) {
            throw new \RuntimeException('اکانت معتبر برای بروزرسانی دامنه پیدا نشد.');
        }

        $client = $this->connections->client($connection);
        $response = $client->updateSendingDomain($accountId, (int) $domain->domain_id, $payload);
        $remote = $response['data'] ?? $response;

        $domain->update([
            'domain_name' => (string) ($remote['domain_name'] ?? ($payload['domain_name'] ?? $domain->domain_name)),
            'dns_verified' => (bool) ($remote['dns_verified'] ?? $domain->dns_verified),
            'dns_verified_at' => $remote['dns_verified_at'] ?? $domain->dns_verified_at,
            'compliance_status' => $remote['compliance_status'] ?? $domain->compliance_status,
            'demo' => (bool) ($remote['demo'] ?? $domain->demo),
            'dns_records' => $remote['dns_records'] ?? $domain->dns_records,
            'settings' => array_merge($domain->settings ?? [], array_filter([
                'open_tracking_enabled' => $remote['open_tracking_enabled'] ?? null,
                'click_tracking_enabled' => $remote['click_tracking_enabled'] ?? null,
                'auto_unsubscribe_link_enabled' => $remote['auto_unsubscribe_link_enabled'] ?? null,
                'custom_domain_tracking_enabled' => $remote['custom_domain_tracking_enabled'] ?? null,
                'health_alerts_enabled' => $remote['health_alerts_enabled'] ?? null,
                'critical_alerts_enabled' => $remote['critical_alerts_enabled'] ?? null,
                'alert_recipient_email' => $remote['alert_recipient_email'] ?? null,
            ], fn ($value) => $value !== null)),
            'metadata' => $remote ?: $domain->metadata,
            'synced_at' => now(),
        ]);

        return $domain->refresh();
    }

    public function delete(MailtrapConnection $connection, MailtrapSendingDomain $domain): void
    {
        $accountId = $this->connections->resolveAccountId($connection);
        if (! $accountId) {
            throw new \RuntimeException('اکانت معتبر برای حذف دامنه پیدا نشد.');
        }

        $client = $this->connections->client($connection);
        $client->deleteSendingDomain($accountId, (int) $domain->domain_id);

        $domain->delete();
    }
}
