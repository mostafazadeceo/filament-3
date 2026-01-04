<?php

namespace Haida\FilamentLoyaltyClub\Models;

use Haida\FilamentLoyaltyClub\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyReferral extends Model
{
    use UsesTenant;

    protected $table = 'loyalty_referrals';

    protected $fillable = [
        'tenant_id',
        'program_id',
        'referrer_customer_id',
        'referee_customer_id',
        'referral_code',
        'referee_phone',
        'referee_email',
        'status',
        'fraud_score',
        'fraud_reason',
        'qualified_at',
        'reward_due_at',
        'rewarded_at',
        'flagged_at',
        'meta',
    ];

    protected $casts = [
        'fraud_score' => 'integer',
        'qualified_at' => 'datetime',
        'reward_due_at' => 'datetime',
        'rewarded_at' => 'datetime',
        'flagged_at' => 'datetime',
        'meta' => 'array',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(LoyaltyReferralProgram::class, 'program_id');
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(LoyaltyCustomer::class, 'referrer_customer_id');
    }

    public function referee(): BelongsTo
    {
        return $this->belongsTo(LoyaltyCustomer::class, 'referee_customer_id');
    }
}
