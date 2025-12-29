<?php

namespace Haida\FilamentPettyCashIr\Models;

use Haida\FilamentPettyCashIr\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentAccountingIr\Models\ChartAccount;
use Vendor\FilamentAccountingIr\Models\TreasuryAccount;

class PettyCashFund extends Model
{
    use HasFactory;
    use SoftDeletes;
    use UsesTenant;

    protected static function booted(): void
    {
        static::creating(function (PettyCashFund $fund): void {
            if ($fund->current_balance === null) {
                $fund->current_balance = $fund->opening_balance ?? 0;
            }
        });
    }

    protected $table = 'petty_cash_funds';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'branch_id',
        'custodian_user_id',
        'accounting_cash_account_id',
        'accounting_source_account_id',
        'default_expense_account_id',
        'accounting_treasury_account_id',
        'name',
        'code',
        'status',
        'currency',
        'opening_balance',
        'current_balance',
        'threshold_balance',
        'replenishment_amount',
        'metadata',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'threshold_balance' => 'decimal:2',
        'replenishment_amount' => 'decimal:2',
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

    public function custodian(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'custodian_user_id');
    }

    public function cashAccount(): BelongsTo
    {
        return $this->belongsTo(ChartAccount::class, 'accounting_cash_account_id');
    }

    public function sourceAccount(): BelongsTo
    {
        return $this->belongsTo(ChartAccount::class, 'accounting_source_account_id');
    }

    public function defaultExpenseAccount(): BelongsTo
    {
        return $this->belongsTo(ChartAccount::class, 'default_expense_account_id');
    }

    public function treasuryAccount(): BelongsTo
    {
        return $this->belongsTo(TreasuryAccount::class, 'accounting_treasury_account_id');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(PettyCashExpense::class, 'fund_id');
    }

    public function replenishments(): HasMany
    {
        return $this->hasMany(PettyCashReplenishment::class, 'fund_id');
    }

    public function settlements(): HasMany
    {
        return $this->hasMany(PettyCashSettlement::class, 'fund_id');
    }
}
