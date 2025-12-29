<?php

namespace Haida\FilamentRestaurantOps\Models;

use Haida\FilamentRestaurantOps\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RestaurantPurchaseOrder extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'restaurant_purchase_orders';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'branch_id',
        'supplier_id',
        'purchase_request_id',
        'order_no',
        'order_date',
        'expected_at',
        'status',
        'subtotal',
        'tax_total',
        'discount_total',
        'total',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_at' => 'date',
        'subtotal' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'total' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(RestaurantSupplier::class, 'supplier_id');
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(RestaurantPurchaseRequest::class, 'purchase_request_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(RestaurantPurchaseOrderLine::class, 'purchase_order_id');
    }

    public function goodsReceipts(): HasMany
    {
        return $this->hasMany(RestaurantGoodsReceipt::class, 'purchase_order_id');
    }
}
