<?php

namespace Haida\FilamentPos\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosSale extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'store_id',
        'register_id',
        'session_id',
        'device_id',
        'receipt_no',
        'status',
        'payment_status',
        'currency',
        'subtotal',
        'discount_total',
        'tax_total',
        'total',
        'source',
        'idempotency_key',
        'created_by_user_id',
        'completed_at',
        'metadata',
    ];

    protected $casts = [
        'subtotal' => 'decimal:4',
        'discount_total' => 'decimal:4',
        'tax_total' => 'decimal:4',
        'total' => 'decimal:4',
        'completed_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(PosStore::class, 'store_id');
    }

    public function register(): BelongsTo
    {
        return $this->belongsTo(PosRegister::class, 'register_id');
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(PosCashierSession::class, 'session_id');
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(PosDevice::class, 'device_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PosSaleItem::class, 'sale_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(PosSalePayment::class, 'sale_id');
    }

    public function getTable(): string
    {
        return config('filament-pos.tables.sales', 'pos_sales');
    }
}
