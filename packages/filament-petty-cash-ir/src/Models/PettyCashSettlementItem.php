<?php

namespace Haida\FilamentPettyCashIr\Models;

use Haida\FilamentPettyCashIr\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PettyCashSettlementItem extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'petty_cash_settlement_items';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'settlement_id',
        'expense_id',
    ];

    public function settlement(): BelongsTo
    {
        return $this->belongsTo(PettyCashSettlement::class, 'settlement_id');
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(PettyCashExpense::class, 'expense_id');
    }
}
