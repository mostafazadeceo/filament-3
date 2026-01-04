<?php

namespace Haida\FilamentLoyaltyClub\Models;

use Haida\FilamentLoyaltyClub\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoyaltyReferralProgram extends Model
{
    use UsesTenant;

    protected $table = 'loyalty_referral_programs';

    protected $fillable = [
        'tenant_id',
        'name',
        'code_prefix',
        'status',
        'qualification_event',
        'min_purchase_amount',
        'waiting_days',
        'max_per_referrer',
        'period_days',
        'referrer_points',
        'referee_points',
        'referrer_cashback',
        'referee_cashback',
        'reward_type',
        'fraud_rules',
        'valid_from',
        'valid_until',
        'meta',
    ];

    protected $casts = [
        'min_purchase_amount' => 'decimal:4',
        'waiting_days' => 'integer',
        'max_per_referrer' => 'integer',
        'period_days' => 'integer',
        'referrer_points' => 'integer',
        'referee_points' => 'integer',
        'referrer_cashback' => 'decimal:4',
        'referee_cashback' => 'decimal:4',
        'fraud_rules' => 'array',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'meta' => 'array',
    ];

    public function referrals(): HasMany
    {
        return $this->hasMany(LoyaltyReferral::class, 'program_id');
    }
}
