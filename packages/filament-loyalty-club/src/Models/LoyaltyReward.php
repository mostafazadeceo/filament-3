<?php

namespace Haida\FilamentLoyaltyClub\Models;

use Haida\FilamentLoyaltyClub\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoyaltyReward extends Model
{
    use UsesTenant;

    protected $table = 'loyalty_rewards';

    protected $fillable = [
        'tenant_id',
        'name',
        'type',
        'description',
        'points_cost',
        'cashback_cost',
        'inventory',
        'status',
        'valid_from',
        'valid_until',
        'constraints',
        'meta',
    ];

    protected $casts = [
        'points_cost' => 'integer',
        'cashback_cost' => 'decimal:4',
        'inventory' => 'integer',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'constraints' => 'array',
        'meta' => 'array',
    ];

    public function redemptions(): HasMany
    {
        return $this->hasMany(LoyaltyRewardRedemption::class, 'reward_id');
    }

    public function donationPledges(): HasMany
    {
        return $this->hasMany(LoyaltyDonationPledge::class, 'reward_id');
    }
}
