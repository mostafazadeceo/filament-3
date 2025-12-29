<?php

namespace Haida\FilamentRestaurantOps\Models;

use Haida\FilamentRestaurantOps\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestaurantInventoryBalance extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'restaurant_inventory_balances';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'warehouse_id',
        'item_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(RestaurantItem::class, 'item_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(RestaurantWarehouse::class, 'warehouse_id');
    }
}
