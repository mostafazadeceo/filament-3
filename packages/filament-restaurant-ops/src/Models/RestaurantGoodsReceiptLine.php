<?php

namespace Haida\FilamentRestaurantOps\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestaurantGoodsReceiptLine extends Model
{
    use HasFactory;

    protected $table = 'restaurant_goods_receipt_lines';

    protected $fillable = [
        'goods_receipt_id',
        'item_id',
        'uom_id',
        'quantity',
        'unit_cost',
        'tax_rate',
        'tax_amount',
        'batch_no',
        'expires_at',
        'line_total',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_cost' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'expires_at' => 'date',
        'line_total' => 'decimal:2',
    ];

    public function receipt(): BelongsTo
    {
        return $this->belongsTo(RestaurantGoodsReceipt::class, 'goods_receipt_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(RestaurantItem::class, 'item_id');
    }

    public function uom(): BelongsTo
    {
        return $this->belongsTo(RestaurantUom::class, 'uom_id');
    }
}
