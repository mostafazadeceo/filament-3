<?php

namespace Haida\FilamentLoyaltyClub\Models;

use Haida\FilamentLoyaltyClub\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyDonationPledge extends Model
{
    use UsesTenant;

    protected $table = 'loyalty_donation_pledges';

    protected $fillable = [
        'tenant_id',
        'customer_id',
        'reward_id',
        'redemption_id',
        'points_spent',
        'cashback_spent',
        'charity_name',
        'charity_reference',
        'status',
        'pledged_at',
        'fulfilled_at',
        'meta',
    ];

    protected $casts = [
        'points_spent' => 'integer',
        'cashback_spent' => 'decimal:4',
        'pledged_at' => 'datetime',
        'fulfilled_at' => 'datetime',
        'meta' => 'array',
    ];

    public function reward(): BelongsTo
    {
        return $this->belongsTo(LoyaltyReward::class, 'reward_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(LoyaltyCustomer::class, 'customer_id');
    }

    public function redemption(): BelongsTo
    {
        return $this->belongsTo(LoyaltyRewardRedemption::class, 'redemption_id');
    }
}
