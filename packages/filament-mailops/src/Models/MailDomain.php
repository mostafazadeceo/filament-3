<?php

declare(strict_types=1);

namespace Haida\FilamentMailOps\Models;

use Haida\FilamentMailOps\Models\Concerns\UsesTenant;
use Haida\FilamentMailOps\Services\DomainDnsAuditService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MailDomain extends Model
{
    use UsesTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'status',
        'dkim_selector',
        'dkim_public_key',
        'dns_snapshot',
        'sync_status',
        'last_error',
        'mailu_synced_at',
        'dns_health_status',
        'dns_health_score',
        'dns_last_checked_at',
        'dns_issues',
        'comment',
    ];

    protected $casts = [
        'dns_snapshot' => 'array',
        'mailu_synced_at' => 'datetime',
        'dns_last_checked_at' => 'datetime',
        'dns_issues' => 'array',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $domain): void {
            $normalizedName = self::normalizeDomainName($domain->name);
            if ($normalizedName !== null) {
                $domain->name = $normalizedName;
            }
            $domain->dkim_selector = filled($domain->dkim_selector)
                ? strtolower(trim((string) $domain->dkim_selector))
                : 'dkim';
        });
    }

    public function mailboxes(): HasMany
    {
        return $this->hasMany(MailMailbox::class, 'domain_id');
    }

    public function aliases(): HasMany
    {
        return $this->hasMany(MailAlias::class, 'domain_id');
    }

    public function outboundMessages(): HasMany
    {
        return $this->hasMany(MailOutboundMessage::class, 'domain_id');
    }

    public function inboundMessages(): HasMany
    {
        return $this->hasMany(MailInboundMessage::class, 'domain_id');
    }

    public function getTable(): string
    {
        return config('filament-mailops.tables.domains', 'mailops_domains');
    }

    public function dnsAudit(): array
    {
        return app(DomainDnsAuditService::class)->evaluate($this->dns_snapshot);
    }

    public function dnsRecordsForClipboard(): string
    {
        return app(DomainDnsAuditService::class)->recordsAsText($this->dns_snapshot);
    }

    public static function normalizeDomainName(?string $domain): ?string
    {
        if (! is_string($domain)) {
            return null;
        }

        $normalized = strtolower(trim($domain));
        $normalized = rtrim($normalized, '.');

        if ($normalized === '') {
            return null;
        }

        if (function_exists('idn_to_ascii')) {
            $idnaOptions = defined('IDNA_DEFAULT') ? IDNA_DEFAULT : 0;
            $idnaVariant = defined('INTL_IDNA_VARIANT_UTS46') ? INTL_IDNA_VARIANT_UTS46 : 0;
            $ascii = idn_to_ascii($normalized, $idnaOptions, $idnaVariant);
            if (is_string($ascii) && $ascii !== '') {
                $normalized = strtolower(trim($ascii));
            }
        }

        return $normalized;
    }
}
