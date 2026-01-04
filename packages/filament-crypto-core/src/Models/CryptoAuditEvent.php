<?php

namespace Haida\FilamentCryptoCore\Models;

use Haida\FilamentCryptoCore\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;

class CryptoAuditEvent extends Model
{
    use UsesTenant;

    public $timestamps = false;

    protected $fillable = [
        'tenant_id',
        'event_type',
        'subject_type',
        'subject_id',
        'description',
        'meta',
        'created_at',
    ];

    protected $casts = [
        'meta' => 'array',
        'created_at' => 'datetime',
    ];

    public function getTable(): string
    {
        return config('filament-crypto-core.tables.audit_events', 'crypto_audit_events');
    }
}
