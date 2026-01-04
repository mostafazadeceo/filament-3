<?php

namespace Haida\FilamentCommerceCore\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Haida\SiteBuilderCore\Models\Site;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommerceProduct extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'site_id',
        'brand_id',
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
        'metadata',
        'created_by_user_id',
        'updated_by_user_id',
    ];

    protected $casts = [
        'price' => 'decimal:4',
        'compare_at_price' => 'decimal:4',
        'track_inventory' => 'bool',
        'metadata' => 'array',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'site_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(CommerceBrand::class, 'brand_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(CommerceVariant::class, 'product_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            CommerceCategory::class,
            config('filament-commerce-core.tables.category_product', 'commerce_category_product'),
            'product_id',
            'category_id'
        )->withTimestamps();
    }

    public function inventoryItems(): HasMany
    {
        return $this->hasMany(CommerceInventoryItem::class, 'product_id');
    }

    public function prices(): HasMany
    {
        return $this->hasMany(CommercePrice::class, 'product_id');
    }

    public function getTable(): string
    {
        return config('filament-commerce-core.tables.products', 'commerce_products');
    }
}
