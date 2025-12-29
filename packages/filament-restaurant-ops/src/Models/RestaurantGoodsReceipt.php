<?php

namespace Haida\FilamentRestaurantOps\Models;

use Haida\FilamentRestaurantOps\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Vendor\FilamentAccountingIr\Models\JournalEntry;

class RestaurantGoodsReceipt extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'restaurant_goods_receipts';

    protected $fillable = [
        'accounting_journal_entry_id',
        'tenant_id',
        'company_id',
        'branch_id',
        'warehouse_id',
        'supplier_id',
        'purchase_order_id',
        'receipt_no',
        'receipt_date',
        'status',
        'subtotal',
        'tax_total',
        'total',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'receipt_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'total' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(RestaurantSupplier::class, 'supplier_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(RestaurantPurchaseOrder::class, 'purchase_order_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(RestaurantWarehouse::class, 'warehouse_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(RestaurantGoodsReceiptLine::class, 'goods_receipt_id');
    }

    public function accountingJournalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'accounting_journal_entry_id');
    }
}
