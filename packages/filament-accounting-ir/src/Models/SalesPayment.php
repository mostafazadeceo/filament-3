<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesPayment extends Model
{
    use HasFactory;

    protected $table = 'accounting_ir_sales_payments';

    protected $fillable = [
        'sales_invoice_id',
        'treasury_account_id',
        'paid_at',
        'amount',
        'method',
        'reference',
        'metadata',
    ];

    protected $casts = [
        'paid_at' => 'date',
        'amount' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(SalesInvoice::class, 'sales_invoice_id');
    }

    public function treasuryAccount(): BelongsTo
    {
        return $this->belongsTo(TreasuryAccount::class, 'treasury_account_id');
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(SalesAllocation::class, 'sales_payment_id');
    }
}
