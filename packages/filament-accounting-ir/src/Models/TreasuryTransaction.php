<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Vendor\FilamentAccountingIr\Models\Concerns\UsesTenant;

class TreasuryTransaction extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'accounting_ir_treasury_transactions';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'treasury_account_id',
        'transaction_type',
        'transaction_date',
        'amount',
        'currency',
        'reference',
        'description',
        'metadata',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(TreasuryAccount::class, 'treasury_account_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(AccountingCompany::class, 'company_id');
    }
}
