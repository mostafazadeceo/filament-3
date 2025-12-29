<?php

namespace Haida\FilamentPettyCashIr\Models;

use Haida\FilamentPettyCashIr\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentAccountingIr\Models\JournalEntry;
use Vendor\FilamentAccountingIr\Models\TreasuryAccount;
use Vendor\FilamentAccountingIr\Models\TreasuryTransaction;

class PettyCashReplenishment extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'petty_cash_replenishments';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'branch_id',
        'fund_id',
        'requested_by',
        'approved_by',
        'paid_by',
        'source_treasury_account_id',
        'accounting_journal_entry_id',
        'accounting_treasury_transaction_id',
        'request_date',
        'amount',
        'currency',
        'status',
        'approved_at',
        'paid_at',
        'description',
        'metadata',
    ];

    protected $casts = [
        'request_date' => 'date',
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(AccountingCompany::class, 'company_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(AccountingBranch::class, 'branch_id');
    }

    public function fund(): BelongsTo
    {
        return $this->belongsTo(PettyCashFund::class, 'fund_id');
    }

    public function treasuryAccount(): BelongsTo
    {
        return $this->belongsTo(TreasuryAccount::class, 'source_treasury_account_id');
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'accounting_journal_entry_id');
    }

    public function treasuryTransaction(): BelongsTo
    {
        return $this->belongsTo(TreasuryTransaction::class, 'accounting_treasury_transaction_id');
    }

    public function requestedByUser(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'requested_by');
    }

    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    public function paidByUser(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'paid_by');
    }
}
