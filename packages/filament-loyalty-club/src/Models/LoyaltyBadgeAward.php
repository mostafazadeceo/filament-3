<?php

namespace Haida\FilamentLoyaltyClub\Models;

use Haida\FilamentLoyaltyClub\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyBadgeAward extends Model
{
    use UsesTenant;

    protected $table = 'loyalty_badge_awards';

    protected $fillable = [
        'tenant_id',
        'badge_id',
        'customer_id',
        'source',
        'awarded_at',
        'meta',
    ];

    protected $casts = [
        'awarded_at' => 'datetime',
        'meta' => 'array',
    ];

    public function badge(): BelongsTo
    {
        return $this->belongsTo(LoyaltyBadge::class, 'badge_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(LoyaltyCustomer::class, 'customer_id');
    }
}
