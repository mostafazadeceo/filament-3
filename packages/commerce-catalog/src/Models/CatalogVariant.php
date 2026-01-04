<?php

namespace Haida\CommerceCatalog\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Vendor\FilamentAccountingIr\Models\InventoryItem;

class CatalogVariant extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'product_id',
        'name',
        'sku',
        'currency',
        'price',
        'is_default',
        'inventory_item_id',
        'attributes',
        'metadata',
    ];

    protected $casts = [
        'price' => 'decimal:4',
        'is_default' => 'bool',
        'attributes' => 'array',
        'metadata' => 'array',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(CatalogProduct::class, 'product_id');
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function getTable(): string
    {
        return config('commerce-catalog.tables.variants', 'commerce_catalog_variants');
    }
}
