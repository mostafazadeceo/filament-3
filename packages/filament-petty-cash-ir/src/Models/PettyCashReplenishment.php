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
        'workflow_rule_id',
        'approval_steps_required',
        'approval_steps_completed',
        'approval_history',
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
        'reversed_at',
        'reversed_by',
        'reversal_journal_entry_id',
        'reversal_reason',
        'description',
        'metadata',
    ];

    protected $casts = [
        'request_date' => 'date',
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
        'reversed_at' => 'datetime',
        'approval_steps_required' => 'int',
        'approval_steps_completed' => 'int',
        'approval_history' => 'array',
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

    public function workflowRule(): BelongsTo
    {
        return $this->belongsTo(PettyCashWorkflowRule::class, 'workflow_rule_id');
    }

    public function treasuryAccount(): BelongsTo
    {
        return $this->belongsTo(TreasuryAccount::class, 'source_treasury_account_id');
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'accounting_journal_entry_id');
    }

    public function reversalJournalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'reversal_journal_entry_id');
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

    public function reversedByUser(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'reversed_by');
    }
}
