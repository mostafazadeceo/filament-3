<?php

namespace Haida\FilamentRestaurantOps\Models;

use Haida\FilamentRestaurantOps\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Vendor\FilamentAccountingIr\Models\InventoryDoc;

class RestaurantInventoryDoc extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'restaurant_inventory_docs';

    protected $fillable = [
        'accounting_inventory_doc_id',
        'tenant_id',
        'company_id',
        'branch_id',
        'warehouse_id',
        'doc_no',
        'doc_type',
        'status',
        'doc_date',
        'reference_type',
        'reference_id',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'doc_date' => 'date',
        'metadata' => 'array',
    ];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(RestaurantWarehouse::class, 'warehouse_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(RestaurantInventoryDocLine::class, 'inventory_doc_id');
    }

    public function accountingInventoryDoc(): BelongsTo
    {
        return $this->belongsTo(InventoryDoc::class, 'accounting_inventory_doc_id');
    }
}
