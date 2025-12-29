<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Vendor\FilamentAccountingIr\Models\Concerns\UsesTenant;

class StockMove extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'accounting_ir_stock_moves';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'inventory_item_id',
        'inventory_doc_id',
        'quantity',
        'unit_cost',
        'direction',
        'move_date',
        'metadata',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_cost' => 'decimal:4',
        'move_date' => 'date',
        'metadata' => 'array',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function inventoryDoc(): BelongsTo
    {
        return $this->belongsTo(InventoryDoc::class, 'inventory_doc_id');
    }
}
