<?php

namespace Haida\TenancyDomains\Models;

use Filamat\IamSuite\Models\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteDomain extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_VERIFIED = 'verified';
    public const STATUS_FAILED = 'failed';

    public const TLS_STATUS_NOT_REQUESTED = 'not_requested';
    public const TLS_STATUS_PENDING = 'pending';
    public const TLS_STATUS_ISSUED = 'issued';
    public const TLS_STATUS_FAILED = 'failed';

    protected $guarded = [];

    protected $casts = [
        'verified_at' => 'datetime',
        'last_checked_at' => 'datetime',
        'is_primary' => 'bool',
        'tls_requested_at' => 'datetime',
        'tls_last_attempted_at' => 'datetime',
        'tls_issued_at' => 'datetime',
        'tls_expires_at' => 'datetime',
    ];

    public function getTable(): string
    {
        return config('tenancy-domains.tables.site_domains', 'site_domains');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
