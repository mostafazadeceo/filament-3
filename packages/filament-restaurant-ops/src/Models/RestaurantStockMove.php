<?php

namespace Haida\FilamentRestaurantOps\Models;

use Haida\FilamentRestaurantOps\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestaurantStockMove extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'restaurant_stock_moves';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'warehouse_id',
        'item_id',
        'inventory_doc_id',
        'direction',
        'quantity',
        'unit_cost',
        'move_date',
        'batch_no',
        'expires_at',
        'metadata',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_cost' => 'decimal:2',
        'move_date' => 'date',
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

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(RestaurantWarehouse::class, 'warehouse_id');
    }
}
