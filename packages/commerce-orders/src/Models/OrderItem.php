<?php

namespace Haida\CommerceOrders\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Haida\CommerceCatalog\Models\CatalogProduct;
use Haida\CommerceCatalog\Models\CatalogVariant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'order_id',
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

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
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
        return config('commerce-orders.tables.order_items', 'commerce_order_items');
    }
}
