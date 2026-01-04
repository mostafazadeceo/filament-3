<?php

namespace Haida\FilamentLoyaltyClub\Models;

use Haida\FilamentLoyaltyClub\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoyaltyCoupon extends Model
{
    use UsesTenant;

    protected $table = 'loyalty_coupons';

    protected $fillable = [
        'tenant_id',
        'reward_id',
        'issued_to_customer_id',
        'code',
        'type',
        'discount_type',
        'discount_value',
        'currency',
        'max_uses',
        'max_uses_per_customer',
        'used_count',
        'stackable',
        'status',
        'source',
        'valid_from',
        'valid_until',
        'constraints',
        'meta',
    ];

    protected $casts = [
        'discount_value' => 'decimal:4',
        'max_uses' => 'integer',
        'max_uses_per_customer' => 'integer',
        'used_count' => 'integer',
        'stackable' => 'boolean',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'constraints' => 'array',
        'meta' => 'array',
    ];

    public function reward(): BelongsTo
    {
        return $this->belongsTo(LoyaltyReward::class, 'reward_id');
    }

    public function issuedTo(): BelongsTo
    {
        return $this->belongsTo(LoyaltyCustomer::class, 'issued_to_customer_id');
    }

    public function redemptions(): HasMany
    {
        return $this->hasMany(LoyaltyCouponRedemption::class, 'coupon_id');
    }
}
