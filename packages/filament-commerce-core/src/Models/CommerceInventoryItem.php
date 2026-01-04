<?php

namespace Haida\FilamentCommerceCore\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommerceInventoryItem extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'product_id',
        'variant_id',
        'sku',
        'location_label',
        'quantity_on_hand',
        'quantity_reserved',
        'status',
        'metadata',
    ];

    protected $casts = [
        'quantity_on_hand' => 'decimal:4',
        'quantity_reserved' => 'decimal:4',
        'metadata' => 'array',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(CommerceProduct::class, 'product_id');
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(CommerceVariant::class, 'variant_id');
    }

    public function stockMoves(): HasMany
    {
        return $this->hasMany(CommerceStockMove::class, 'inventory_item_id');
    }

    public function getTable(): string
    {
        return config('filament-commerce-core.tables.inventory_items', 'commerce_inventory_items');
    }
}
