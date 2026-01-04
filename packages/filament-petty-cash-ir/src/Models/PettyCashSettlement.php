<?php

namespace Haida\FilamentPettyCashIr\Models;

use Haida\FilamentPettyCashIr\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentAccountingIr\Models\JournalEntry;

class PettyCashSettlement extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'petty_cash_settlements';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'branch_id',
        'fund_id',
        'requested_by',
        'approved_by',
        'posted_by',
        'accounting_journal_entry_id',
        'period_start',
        'period_end',
        'status',
        'total_expenses',
        'total_replenished',
        'approved_at',
        'posted_at',
        'reversed_at',
        'reversed_by',
        'reversal_reason',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'total_expenses' => 'decimal:2',
        'total_replenished' => 'decimal:2',
        'approved_at' => 'datetime',
        'posted_at' => 'datetime',
        'reversed_at' => 'datetime',
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

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'accounting_journal_entry_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PettyCashSettlementItem::class, 'settlement_id');
    }

    public function requestedByUser(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'requested_by');
    }

    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    public function postedByUser(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'posted_by');
    }

    public function reversedByUser(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'reversed_by');
    }
}
