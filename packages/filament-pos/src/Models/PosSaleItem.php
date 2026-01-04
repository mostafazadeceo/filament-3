<?php

namespace Haida\FilamentPos\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosSaleItem extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'sale_id',
        'product_id',
        'variant_id',
        'name',
        'sku',
        'barcode',
        'quantity',
        'unit_price',
        'discount_amount',
        'tax_amount',
        'total',
        'metadata',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_price' => 'decimal:4',
        'discount_amount' => 'decimal:4',
        'tax_amount' => 'decimal:4',
        'total' => 'decimal:4',
        'metadata' => 'array',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(PosSale::class, 'sale_id');
    }

    public function getTable(): string
    {
        return config('filament-pos.tables.sale_items', 'pos_sale_items');
    }
}
