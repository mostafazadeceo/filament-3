<?php

namespace Haida\FilamentLoyaltyClub\Models;

use Haida\FilamentLoyaltyClub\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyFraudSignal extends Model
{
    use UsesTenant;

    protected $table = 'loyalty_fraud_signals';

    protected $fillable = [
        'tenant_id',
        'customer_id',
        'type',
        'severity',
        'status',
        'subject_type',
        'subject_id',
        'score',
        'reviewed_by',
        'detected_at',
        'reviewed_at',
        'resolution',
        'meta',
    ];

    protected $casts = [
        'score' => 'integer',
        'detected_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'meta' => 'array',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(LoyaltyCustomer::class, 'customer_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'reviewed_by');
    }
}
