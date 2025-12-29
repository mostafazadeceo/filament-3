<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Vendor\FilamentAccountingIr\Models\Concerns\UsesTenant;

class SalesInvoice extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'accounting_ir_sales_invoices';

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
        'is_official',
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
        'is_official' => 'bool',
        'metadata' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(AccountingCompany::class, 'company_id');
    }

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
        return $this->hasMany(SalesInvoiceLine::class, 'sales_invoice_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SalesPayment::class, 'sales_invoice_id');
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(SalesAllocation::class, 'sales_invoice_id');
    }

    public function eInvoices(): HasMany
    {
        return $this->hasMany(EInvoice::class, 'sales_invoice_id');
    }
}
