<?php

namespace Haida\FilamentPos\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosSalePayment extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'sale_id',
        'provider',
        'amount',
        'currency',
        'status',
        'reference',
        'processed_at',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:4',
        'processed_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(PosSale::class, 'sale_id');
    }

    public function getTable(): string
    {
        return config('filament-pos.tables.sale_payments', 'pos_sale_payments');
    }
}
