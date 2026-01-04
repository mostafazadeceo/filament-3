<?php

namespace Haida\FilamentPettyCashIr\Models;

use Haida\FilamentPettyCashIr\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PettyCashWorkflowRule extends Model
{
    use UsesTenant;

    protected $table = 'petty_cash_workflow_rules';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'fund_id',
        'category_id',
        'transaction_type',
        'min_amount',
        'max_amount',
        'steps_required',
        'require_separation',
        'require_receipt',
        'status',
        'metadata',
    ];

    protected $casts = [
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'steps_required' => 'int',
        'require_separation' => 'bool',
        'metadata' => 'array',
    ];

    public function fund(): BelongsTo
    {
        return $this->belongsTo(PettyCashFund::class, 'fund_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PettyCashCategory::class, 'category_id');
    }
}
