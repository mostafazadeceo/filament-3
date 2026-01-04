<?php

namespace Haida\FilamentPettyCashIr\Models;

use Haida\FilamentPettyCashIr\Models\Concerns\UsesTenant;
use Haida\FilamentPettyCashIr\Services\PettyCashControlService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PettyCashReconciliation extends Model
{
    use UsesTenant;

    protected $table = 'petty_cash_reconciliations';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'fund_id',
        'period_start',
        'period_end',
        'status',
        'expected_balance',
        'ledger_balance',
        'variance',
        'prepared_by',
        'approved_by',
        'approved_at',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'expected_balance' => 'decimal:2',
        'ledger_balance' => 'decimal:2',
        'variance' => 'decimal:2',
        'approved_at' => 'datetime',
        'metadata' => 'array',
    ];

    protected static function booted(): void
    {
        static::saving(function (PettyCashReconciliation $reconciliation): void {
            $reconciliation->variance = (float) $reconciliation->ledger_balance - (float) $reconciliation->expected_balance;
        });

        static::saved(function (PettyCashReconciliation $reconciliation): void {
            if ((float) $reconciliation->variance === 0.0) {
                return;
            }

            app(PettyCashControlService::class)->recordException(
                'reconciliation_variance',
                'مغایرت تسویه تنخواه',
                'high',
                $reconciliation,
                [
                    'expected_balance' => (float) $reconciliation->expected_balance,
                    'ledger_balance' => (float) $reconciliation->ledger_balance,
                    'variance' => (float) $reconciliation->variance,
                    'description' => 'مغایرت در فرآیند تطبیق ثبت شده است.',
                ],
                $reconciliation->fund_id
            );
        });
    }

    public function fund(): BelongsTo
    {
        return $this->belongsTo(PettyCashFund::class, 'fund_id');
    }

    public function preparedByUser(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'prepared_by');
    }

    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }
}
