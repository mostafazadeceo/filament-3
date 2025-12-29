<?php

namespace Haida\FilamentRestaurantOps\Models;

use Haida\FilamentRestaurantOps\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Vendor\FilamentAccountingIr\Models\InventoryItem;

class RestaurantItem extends Model
{
    use HasFactory;
    use SoftDeletes;
    use UsesTenant;

    protected $table = 'restaurant_items';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'accounting_inventory_item_id',
        'name',
        'code',
        'category',
        'is_active',
        'base_uom_id',
        'purchase_uom_id',
        'consumption_uom_id',
        'purchase_to_base_rate',
        'consumption_to_base_rate',
        'min_stock',
        'max_stock',
        'reorder_point',
        'track_batch',
        'track_expiry',
        'metadata',
    ];

    protected $casts = [
        'is_active' => 'bool',
        'purchase_to_base_rate' => 'decimal:4',
        'consumption_to_base_rate' => 'decimal:4',
        'min_stock' => 'decimal:4',
        'max_stock' => 'decimal:4',
        'reorder_point' => 'decimal:4',
        'track_batch' => 'bool',
        'track_expiry' => 'bool',
        'metadata' => 'array',
    ];

    public function baseUom(): BelongsTo
    {
        return $this->belongsTo(RestaurantUom::class, 'base_uom_id');
    }

    public function purchaseUom(): BelongsTo
    {
        return $this->belongsTo(RestaurantUom::class, 'purchase_uom_id');
    }

    public function consumptionUom(): BelongsTo
    {
        return $this->belongsTo(RestaurantUom::class, 'consumption_uom_id');
    }

    public function balances(): HasMany
    {
        return $this->hasMany(RestaurantInventoryBalance::class, 'item_id');
    }

    public function accountingItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'accounting_inventory_item_id');
    }
}
