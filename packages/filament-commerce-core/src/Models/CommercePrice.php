<?php

namespace Haida\FilamentCommerceCore\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommercePrice extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'price_list_id',
        'product_id',
        'variant_id',
        'currency',
        'price',
        'compare_at_price',
        'starts_at',
        'ends_at',
        'metadata',
    ];

    protected $casts = [
        'price' => 'decimal:4',
        'compare_at_price' => 'decimal:4',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function priceList(): BelongsTo
    {
        return $this->belongsTo(CommercePriceList::class, 'price_list_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(CommerceProduct::class, 'product_id');
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(CommerceVariant::class, 'variant_id');
    }

    public function getTable(): string
    {
        return config('filament-commerce-core.tables.prices', 'commerce_prices');
    }
}
