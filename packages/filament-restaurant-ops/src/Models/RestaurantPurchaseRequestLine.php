<?php

namespace Haida\FilamentRestaurantOps\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestaurantPurchaseRequestLine extends Model
{
    use HasFactory;

    protected $table = 'restaurant_purchase_request_lines';

    protected $fillable = [
        'purchase_request_id',
        'item_id',
        'uom_id',
        'quantity',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(RestaurantPurchaseRequest::class, 'purchase_request_id');
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
