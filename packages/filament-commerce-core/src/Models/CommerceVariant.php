<?php

namespace Haida\FilamentCommerceCore\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommerceVariant extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'product_id',
        'name',
        'sku',
        'barcode',
        'status',
        'currency',
        'price',
        'compare_at_price',
        'attributes',
        'metadata',
        'created_by_user_id',
        'updated_by_user_id',
    ];

    protected $casts = [
        'price' => 'decimal:4',
        'compare_at_price' => 'decimal:4',
        'attributes' => 'array',
        'metadata' => 'array',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(CommerceProduct::class, 'product_id');
    }

    public function inventoryItems(): HasMany
    {
        return $this->hasMany(CommerceInventoryItem::class, 'variant_id');
    }

    public function prices(): HasMany
    {
        return $this->hasMany(CommercePrice::class, 'variant_id');
    }

    public function getTable(): string
    {
        return config('filament-commerce-core.tables.variants', 'commerce_variants');
    }
}
