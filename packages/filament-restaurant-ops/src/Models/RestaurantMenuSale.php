<?php

namespace Haida\FilamentRestaurantOps\Models;

use Haida\FilamentRestaurantOps\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentAccountingIr\Models\JournalEntry;

class RestaurantMenuSale extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'restaurant_menu_sales';

    protected $fillable = [
        'accounting_journal_entry_id',
        'tenant_id',
        'company_id',
        'branch_id',
        'warehouse_id',
        'sale_date',
        'source',
        'external_ref',
        'total_amount',
        'status',
    ];

    protected $casts = [
        'sale_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function lines(): HasMany
    {
        return $this->hasMany(RestaurantMenuSaleLine::class, 'menu_sale_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(AccountingCompany::class, 'company_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(AccountingBranch::class, 'branch_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(RestaurantWarehouse::class, 'warehouse_id');
    }

    public function accountingJournalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'accounting_journal_entry_id');
    }
}
