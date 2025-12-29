<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Vendor\FilamentAccountingIr\Models\Concerns\UsesTenant;

class PurchaseInvoice extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'accounting_ir_purchase_invoices';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'branch_id',
        'fiscal_year_id',
        'party_id',
        'invoice_no',
        'invoice_date',
        'due_date',
        'status',
        'currency',
        'exchange_rate',
        'subtotal',
        'discount_total',
        'tax_total',
        'total',
        'metadata',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'exchange_rate' => 'decimal:6',
        'subtotal' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'total' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class, 'party_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(AccountingBranch::class, 'branch_id');
    }

    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(FiscalYear::class, 'fiscal_year_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(PurchaseInvoiceLine::class, 'purchase_invoice_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(PurchasePayment::class, 'purchase_invoice_id');
    }
}
