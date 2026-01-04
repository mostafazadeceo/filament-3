<?php

namespace Haida\FilamentLoyaltyClub\Models;

use Haida\FilamentLoyaltyClub\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyCouponRedemption extends Model
{
    use UsesTenant;

    protected $table = 'loyalty_coupon_redemptions';

    protected $fillable = [
        'tenant_id',
        'coupon_id',
        'customer_id',
        'order_reference',
        'status',
        'meta',
        'redeemed_at',
    ];

    protected $casts = [
        'meta' => 'array',
        'redeemed_at' => 'datetime',
    ];

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(LoyaltyCoupon::class, 'coupon_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(LoyaltyCustomer::class, 'customer_id');
    }
}
