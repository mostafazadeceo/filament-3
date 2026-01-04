<?php

namespace Haida\FilamentPettyCashIr\Models;

use Haida\FilamentPettyCashIr\Models\Concerns\UsesTenant;
use Haida\FilamentPettyCashIr\Services\PettyCashControlService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PettyCashCashCount extends Model
{
    use UsesTenant;

    protected $table = 'petty_cash_cash_counts';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'fund_id',
        'count_date',
        'status',
        'expected_balance',
        'counted_balance',
        'variance',
        'counted_by',
        'approved_by',
        'approved_at',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'count_date' => 'date',
        'expected_balance' => 'decimal:2',
        'counted_balance' => 'decimal:2',
        'variance' => 'decimal:2',
        'approved_at' => 'datetime',
        'metadata' => 'array',
    ];

    protected static function booted(): void
    {
        static::saving(function (PettyCashCashCount $count): void {
            $count->variance = (float) $count->counted_balance - (float) $count->expected_balance;
        });

        static::saved(function (PettyCashCashCount $count): void {
            if ((float) $count->variance === 0.0) {
                return;
            }

            app(PettyCashControlService::class)->recordException(
                'cash_count_variance',
                'مغایرت شمارش نقدی',
                'high',
                $count,
                [
                    'expected_balance' => (float) $count->expected_balance,
                    'counted_balance' => (float) $count->counted_balance,
                    'variance' => (float) $count->variance,
                    'description' => 'مغایرت در شمارش نقدی ثبت شده است.',
                ],
                $count->fund_id
            );
        });
    }

    public function fund(): BelongsTo
    {
        return $this->belongsTo(PettyCashFund::class, 'fund_id');
    }

    public function countedByUser(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'counted_by');
    }

    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }
}
