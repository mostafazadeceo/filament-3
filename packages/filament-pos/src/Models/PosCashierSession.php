<?php

namespace Haida\FilamentPos\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosCashierSession extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'store_id',
        'register_id',
        'device_id',
        'opened_by_user_id',
        'closed_by_user_id',
        'status',
        'opened_at',
        'closed_at',
        'opening_float',
        'closing_cash',
        'expected_cash',
        'variance',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'opening_float' => 'decimal:4',
        'closing_cash' => 'decimal:4',
        'expected_cash' => 'decimal:4',
        'variance' => 'decimal:4',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(PosStore::class, 'store_id');
    }

    public function register(): BelongsTo
    {
        return $this->belongsTo(PosRegister::class, 'register_id');
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(PosDevice::class, 'device_id');
    }

    public function movements(): HasMany
    {
        return $this->hasMany(PosCashMovement::class, 'session_id');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(PosSale::class, 'session_id');
    }

    public function getTable(): string
    {
        return config('filament-pos.tables.cashier_sessions', 'pos_cashier_sessions');
    }
}
