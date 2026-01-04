<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MailtrapSendingDomain extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'connection_id',
        'domain_id',
        'domain_name',
        'dns_verified',
        'dns_verified_at',
        'compliance_status',
        'demo',
        'dns_records',
        'settings',
        'metadata',
        'synced_at',
    ];

    protected $casts = [
        'dns_verified' => 'bool',
        'demo' => 'bool',
        'dns_verified_at' => 'datetime',
        'dns_records' => 'array',
        'settings' => 'array',
        'metadata' => 'array',
        'synced_at' => 'datetime',
    ];

    public function connection(): BelongsTo
    {
        return $this->belongsTo(MailtrapConnection::class, 'connection_id');
    }

    public function getTable(): string
    {
        return config('mailtrap-core.tables.sending_domains', 'mailtrap_sending_domains');
    }
}
