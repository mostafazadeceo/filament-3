<?php

namespace Haida\FilamentPettyCashIr\Models;

use Haida\FilamentPettyCashIr\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PettyCashAiSuggestion extends Model
{
    use UsesTenant;

    protected $table = 'petty_cash_ai_suggestions';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'fund_id',
        'subject_type',
        'subject_id',
        'suggestion_type',
        'status',
        'score',
        'provider',
        'input_hash',
        'suggested_payload',
        'reasons',
        'input_payload',
        'output_payload',
        'requested_by',
        'decided_by',
        'decided_at',
        'metadata',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'suggested_payload' => 'array',
        'reasons' => 'array',
        'input_payload' => 'array',
        'output_payload' => 'array',
        'metadata' => 'array',
        'decided_at' => 'datetime',
    ];

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function requestedByUser(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'requested_by');
    }

    public function decidedByUser(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'decided_by');
    }
}
