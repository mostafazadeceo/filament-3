<?php

namespace Haida\FilamentRestaurantOps\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestaurantInventoryDocLine extends Model
{
    use HasFactory;

    protected $table = 'restaurant_inventory_doc_lines';

    protected $fillable = [
        'inventory_doc_id',
        'item_id',
        'uom_id',
        'quantity',
        'unit_cost',
        'batch_no',
        'expires_at',
        'metadata',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_cost' => 'decimal:2',
        'expires_at' => 'date',
        'metadata' => 'array',
    ];

    public function doc(): BelongsTo
    {
        return $this->belongsTo(RestaurantInventoryDoc::class, 'inventory_doc_id');
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
