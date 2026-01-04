<?php

namespace Haida\FilamentLoyaltyClub\Models;

use Haida\FilamentLoyaltyClub\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyRewardRedemption extends Model
{
    use UsesTenant;

    protected $table = 'loyalty_reward_redemptions';

    protected $fillable = [
        'tenant_id',
        'reward_id',
        'customer_id',
        'points_spent',
        'cashback_spent',
        'idempotency_key',
        'status',
        'reference_type',
        'reference_id',
        'meta',
        'redeemed_at',
    ];

    protected $casts = [
        'points_spent' => 'integer',
        'cashback_spent' => 'decimal:4',
        'redeemed_at' => 'datetime',
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
}
