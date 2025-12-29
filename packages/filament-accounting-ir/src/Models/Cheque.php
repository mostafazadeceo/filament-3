<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Vendor\FilamentAccountingIr\Models\Concerns\UsesTenant;

class Cheque extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'accounting_ir_cheques';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'party_id',
        'treasury_account_id',
        'direction',
        'cheque_no',
        'bank_name',
        'branch_name',
        'due_date',
        'amount',
        'status',
        'metadata',
    ];

    protected $casts = [
        'due_date' => 'date',
        'amount' => 'decimal:2',
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

    public function treasuryAccount(): BelongsTo
    {
        return $this->belongsTo(TreasuryAccount::class, 'treasury_account_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(ChequeEvent::class, 'cheque_id');
    }
}
