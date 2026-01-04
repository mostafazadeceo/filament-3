<?php

namespace Haida\FilamentPettyCashIr\Models;

use Haida\FilamentPettyCashIr\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PettyCashControlException extends Model
{
    use UsesTenant;

    protected $table = 'petty_cash_control_exceptions';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'fund_id',
        'subject_type',
        'subject_id',
        'rule_code',
        'severity',
        'status',
        'title',
        'description',
        'detected_at',
        'resolved_at',
        'detected_by',
        'resolved_by',
        'metadata',
    ];

    protected $casts = [
        'detected_at' => 'datetime',
        'resolved_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function fund(): BelongsTo
    {
        return $this->belongsTo(PettyCashFund::class, 'fund_id');
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function detectedByUser(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'detected_by');
    }

    public function resolvedByUser(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'resolved_by');
    }
}
