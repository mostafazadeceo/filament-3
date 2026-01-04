<?php

namespace Haida\FilamentPos\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosSyncCursor extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'device_id',
        'cursor',
        'last_synced_at',
        'metadata',
    ];

    protected $casts = [
        'last_synced_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(PosDevice::class, 'device_id');
    }

    public function getTable(): string
    {
        return config('filament-pos.tables.sync_cursors', 'pos_sync_cursors');
    }
}
