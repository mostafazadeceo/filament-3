<?php

namespace Haida\FilamentPos\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosOutbox extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'device_id',
        'event_type',
        'event_id',
        'idempotency_key',
        'status',
        'error_reason',
        'processed_at',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
        'processed_at' => 'datetime',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(PosDevice::class, 'device_id');
    }

    public function getTable(): string
    {
        return config('filament-pos.tables.outbox', 'pos_outbox');
    }
}
