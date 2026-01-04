<?php

namespace Haida\CommerceCatalog\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Haida\SiteBuilderCore\Models\Site;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Vendor\FilamentAccountingIr\Models\InventoryItem;
use Vendor\FilamentAccountingIr\Models\ProductService;

class CatalogProduct extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'site_id',
        'name',
        'slug',
        'type',
        'status',
        'sku',
        'summary',
        'description',
        'currency',
        'price',
        'compare_at_price',
        'track_inventory',
        'accounting_product_id',
        'inventory_item_id',
        'metadata',
        'published_at',
        'created_by_user_id',
        'updated_by_user_id',
    ];

    protected $casts = [
        'price' => 'decimal:4',
        'compare_at_price' => 'decimal:4',
        'track_inventory' => 'bool',
        'metadata' => 'array',
        'published_at' => 'datetime',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'site_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(CatalogVariant::class, 'product_id');
    }

    public function media(): HasMany
    {
        return $this->hasMany(CatalogMedia::class, 'product_id');
    }

    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(
            CatalogCollection::class,
            config('commerce-catalog.tables.collection_product', 'commerce_catalog_collection_product'),
            'product_id',
            'collection_id'
        )->withTimestamps();
    }

    public function accountingProduct(): BelongsTo
    {
        return $this->belongsTo(ProductService::class, 'accounting_product_id');
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function getTable(): string
    {
        return config('commerce-catalog.tables.products', 'commerce_catalog_products');
    }
}
