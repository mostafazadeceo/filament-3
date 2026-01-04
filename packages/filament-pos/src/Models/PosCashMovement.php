<?php

namespace Haida\FilamentPos\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosCashMovement extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'session_id',
        'type',
        'amount',
        'reason',
        'recorded_at',
        'created_by_user_id',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:4',
        'recorded_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(PosCashierSession::class, 'session_id');
    }

    public function getTable(): string
    {
        return config('filament-pos.tables.cash_movements', 'pos_cash_movements');
    }
}
