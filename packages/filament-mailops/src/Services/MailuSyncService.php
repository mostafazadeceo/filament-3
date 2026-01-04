<?php

declare(strict_types=1);

namespace Haida\FilamentMailOps\Services;

use Haida\FilamentMailOps\Models\MailAlias;
use Haida\FilamentMailOps\Models\MailDomain;
use Haida\FilamentMailOps\Models\MailMailbox;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Throwable;

class MailuSyncService
{
    public function __construct(protected MailuClient $client) {}

    public function syncDomain(MailDomain $domain): MailDomain
    {
        try {
            $response = $this->client->createDomain([
                'name' => $domain->name,
                'comment' => $domain->comment,
            ]);

            $this->throwUnlessConflict($response);

            $dns = $this->safeDomainDnsSnapshot($domain->name);

            $domain->update([
                'sync_status' => 'synced',
                'last_error' => null,
                'mailu_synced_at' => now(),
                'dns_snapshot' => $dns ?: $domain->dns_snapshot,
            ]);
        } catch (Throwable $exception) {
            $this->markFailed($domain, $exception);
        }

        return $domain->refresh();
    }

    public function syncMailbox(MailMailbox $mailbox, ?string $rawPassword = null): MailMailbox
    {
        try {
            $payload = $this->mailboxPayload($mailbox, $rawPassword);

            if ($rawPassword) {
                $response = $this->client->createUser($payload);
                if ($response->status() === 409) {
                    $updatePayload = $this->mailboxPayload($mailbox, $rawPassword);
                    $this->client->updateUser($mailbox->email, $updatePayload)->throw();
                } else {
                    $response->throw();
                }
            } else {
                $response = $this->client->updateUser($mailbox->email, $this->mailboxPayload($mailbox, null));
                if ($response->status() === 404) {
                    throw new \RuntimeException('Mailbox does not exist on Mailu; password required to create.');
                }
                $response->throw();
            }

            $mailbox->update([
                'sync_status' => 'synced',
                'last_error' => null,
                'mailu_synced_at' => now(),
            ]);
        } catch (Throwable $exception) {
            $this->markFailed($mailbox, $exception);
        }

        return $mailbox->refresh();
    }

    public function syncAlias(MailAlias $alias): MailAlias
    {
        try {
            $payload = $this->aliasPayload($alias);
            $response = $this->client->createAlias($payload);

            if ($response->status() === 409) {
                $this->client->updateAlias($alias->source, Arr::except($payload, ['email']))->throw();
            } else {
                $response->throw();
            }

            $alias->update([
                'sync_status' => 'synced',
                'last_error' => null,
                'mailu_synced_at' => now(),
            ]);
        } catch (Throwable $exception) {
            $this->markFailed($alias, $exception);
        }

        return $alias->refresh();
    }

    protected function mailboxPayload(MailMailbox $mailbox, ?string $rawPassword): array
    {
        $settings = $mailbox->settings ?? [];

        $payload = [
            'email' => $mailbox->email,
            'raw_password' => $rawPassword,
            'displayed_name' => $mailbox->display_name,
            'comment' => $mailbox->comment,
            'quota_bytes' => $mailbox->quota_bytes,
            'enabled' => $mailbox->status === 'active',
            'enable_imap' => (bool) ($settings['enable_imap'] ?? true),
            'enable_pop' => (bool) ($settings['enable_pop'] ?? false),
            'allow_spoofing' => (bool) ($settings['allow_spoofing'] ?? false),
            'forward_enabled' => (bool) ($settings['forward_enabled'] ?? false),
            'forward_destination' => array_values(array_filter($settings['forward_destination'] ?? [])),
            'forward_keep' => (bool) ($settings['forward_keep'] ?? true),
            'reply_enabled' => (bool) ($settings['reply_enabled'] ?? false),
            'reply_subject' => $settings['reply_subject'] ?? null,
            'reply_body' => $settings['reply_body'] ?? null,
        ];

        return array_filter($payload, static fn ($value) => $value !== null);
    }

    protected function aliasPayload(MailAlias $alias): array
    {
        return [
            'email' => $alias->source,
            'destination' => array_values(array_filter($alias->destinations ?? [])),
            'comment' => $alias->comment,
            'wildcard' => (bool) $alias->is_wildcard,
        ];
    }

    protected function safeDomainDnsSnapshot(string $domain): ?array
    {
        $response = $this->client->getDomain($domain);
        if (! $response->successful()) {
            return null;
        }

        $payload = $response->json();
        if (! is_array($payload)) {
            return null;
        }

        return [
            'dns_mx' => $payload['dns_mx'] ?? null,
            'dns_spf' => $payload['dns_spf'] ?? null,
            'dns_dkim' => $payload['dns_dkim'] ?? null,
            'dns_dmarc' => $payload['dns_dmarc'] ?? null,
            'dns_dmarc_report' => $payload['dns_dmarc_report'] ?? null,
            'dns_tlsa' => $payload['dns_tlsa'] ?? null,
            'dns_autoconfig' => $payload['dns_autoconfig'] ?? null,
        ];
    }

    protected function throwUnlessConflict(Response $response): void
    {
        if ($response->status() === 409) {
            return;
        }

        $response->throw();
    }

    protected function markFailed(Model $model, Throwable $exception): void
    {
        $message = Str::limit($exception->getMessage(), 500);

        $model->update([
            'sync_status' => 'failed',
            'last_error' => $message,
        ]);
    }
}
