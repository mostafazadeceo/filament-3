<?php

namespace Haida\CommerceOrders\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Haida\CommerceCatalog\Models\CatalogProduct;
use Haida\CommerceCatalog\Models\CatalogVariant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderReturnItem extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'order_return_id',
        'order_item_id',
        'product_id',
        'variant_id',
        'name',
        'sku',
        'quantity',
        'reason',
        'status',
        'meta',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'meta' => 'array',
    ];

    public function orderReturn(): BelongsTo
    {
        return $this->belongsTo(OrderReturn::class, 'order_return_id');
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
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
        return config('commerce-orders.tables.order_return_items', 'commerce_order_return_items');
    }
}
