<?php

namespace Haida\FilamentPos\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosRegister extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'store_id',
        'name',
        'code',
        'status',
        'last_opened_at',
        'last_closed_at',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'last_opened_at' => 'datetime',
        'last_closed_at' => 'datetime',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(PosStore::class, 'store_id');
    }

    public function devices(): HasMany
    {
        return $this->hasMany(PosDevice::class, 'register_id');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(PosCashierSession::class, 'register_id');
    }

    public function getTable(): string
    {
        return config('filament-pos.tables.registers', 'pos_registers');
    }
}
