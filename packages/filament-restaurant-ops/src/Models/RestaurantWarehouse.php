<?php

namespace Haida\FilamentRestaurantOps\Models;

use Haida\FilamentRestaurantOps\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Vendor\FilamentAccountingIr\Models\InventoryWarehouse;

class RestaurantWarehouse extends Model
{
    use HasFactory;
    use SoftDeletes;
    use UsesTenant;

    protected $table = 'restaurant_warehouses';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'accounting_inventory_warehouse_id',
        'branch_id',
        'name',
        'code',
        'type',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'is_active' => 'bool',
        'metadata' => 'array',
    ];

    public function balances(): HasMany
    {
        return $this->hasMany(RestaurantInventoryBalance::class, 'warehouse_id');
    }

    public function accountingWarehouse(): BelongsTo
    {
        return $this->belongsTo(InventoryWarehouse::class, 'accounting_inventory_warehouse_id');
    }
}
