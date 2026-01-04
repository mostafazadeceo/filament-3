<?php

namespace Haida\FilamentPettyCashIr\Models;

use Haida\FilamentPettyCashIr\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentAccountingIr\Models\JournalEntry;
use Vendor\FilamentAccountingIr\Models\Party;

class PettyCashExpense extends Model
{
    use HasFactory;
    use SoftDeletes;
    use UsesTenant;

    protected $table = 'petty_cash_expenses';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'branch_id',
        'fund_id',
        'category_id',
        'workflow_rule_id',
        'approval_steps_required',
        'approval_steps_completed',
        'approval_history',
        'requested_by',
        'approved_by',
        'paid_by',
        'accounting_party_id',
        'accounting_journal_entry_id',
        'expense_date',
        'amount',
        'currency',
        'status',
        'reference',
        'payee_name',
        'description',
        'receipt_required',
        'has_receipt',
        'approved_at',
        'paid_at',
        'reversed_at',
        'reversed_by',
        'reversal_journal_entry_id',
        'reversal_reason',
        'metadata',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
        'receipt_required' => 'bool',
        'has_receipt' => 'bool',
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(PettyCashCategory::class, 'category_id');
    }

    public function workflowRule(): BelongsTo
    {
        return $this->belongsTo(PettyCashWorkflowRule::class, 'workflow_rule_id');
    }

    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class, 'accounting_party_id');
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'accounting_journal_entry_id');
    }

    public function reversalJournalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'reversal_journal_entry_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(PettyCashExpenseAttachment::class, 'expense_id');
    }

    public function settlementItem(): HasOne
    {
        return $this->hasOne(PettyCashSettlementItem::class, 'expense_id');
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
