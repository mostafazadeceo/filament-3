<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryDocLine extends Model
{
    use HasFactory;

    protected $table = 'accounting_ir_inventory_doc_lines';

    protected $fillable = [
        'inventory_doc_id',
        'inventory_item_id',
        'location_id',
        'quantity',
        'unit_cost',
        'metadata',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_cost' => 'decimal:4',
        'metadata' => 'array',
    ];

    public function inventoryDoc(): BelongsTo
    {
        return $this->belongsTo(InventoryDoc::class, 'inventory_doc_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(InventoryLocation::class, 'location_id');
    }
}
