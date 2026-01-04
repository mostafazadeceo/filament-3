<?php

declare(strict_types=1);

namespace Haida\FilamentMailOps\Models;

use Haida\FilamentMailOps\Models\Concerns\UsesTenant;
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
        'comment',
    ];

    protected $casts = [
        'dns_snapshot' => 'array',
        'mailu_synced_at' => 'datetime',
    ];

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
}
