<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Vendor\FilamentAccountingIr\Models\Concerns\UsesTenant;

class InventoryItem extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'accounting_ir_inventory_items';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'product_id',
        'sku',
        'min_stock',
        'current_stock',
        'allow_negative',
        'metadata',
    ];

    protected $casts = [
        'min_stock' => 'decimal:4',
        'current_stock' => 'decimal:4',
        'allow_negative' => 'bool',
        'metadata' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(AccountingCompany::class, 'company_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(ProductService::class, 'product_id');
    }

    public function stockMoves(): HasMany
    {
        return $this->hasMany(StockMove::class, 'inventory_item_id');
    }
}
