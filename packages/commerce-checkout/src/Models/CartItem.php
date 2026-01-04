<?php

namespace Haida\CommerceCheckout\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Haida\CommerceCatalog\Models\CatalogProduct;
use Haida\CommerceCatalog\Models\CatalogVariant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'cart_id',
        'product_id',
        'variant_id',
        'name',
        'sku',
        'quantity',
        'currency',
        'unit_price',
        'line_total',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'quantity' => 'decimal:4',
        'unit_price' => 'decimal:4',
        'line_total' => 'decimal:4',
    ];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class, 'cart_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(CatalogProduct::class, 'product_id');
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(CatalogVariant::class, 'variant_id');
    }

    public function getTable(): string
    {
        return config('commerce-checkout.tables.cart_items', 'commerce_checkout_cart_items');
    }
}
