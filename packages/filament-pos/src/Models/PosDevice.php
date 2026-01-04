<?php

namespace Haida\FilamentPos\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosDevice extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'register_id',
        'device_uid',
        'status',
        'last_seen_at',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'last_seen_at' => 'datetime',
    ];

    public function register(): BelongsTo
    {
        return $this->belongsTo(PosRegister::class, 'register_id');
    }

    public function getTable(): string
    {
        return config('filament-pos.tables.devices', 'pos_devices');
    }
}
