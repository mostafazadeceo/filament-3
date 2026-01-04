<?php

namespace Haida\FilamentLoyaltyClub\Models;

use Haida\FilamentLoyaltyClub\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoyaltyTier extends Model
{
    use UsesTenant;

    protected $table = 'loyalty_tiers';

    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'rank',
        'threshold_points',
        'threshold_spend',
        'benefits',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'threshold_points' => 'integer',
        'threshold_spend' => 'decimal:4',
        'benefits' => 'array',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function customers(): HasMany
    {
        return $this->hasMany(LoyaltyCustomer::class, 'tier_id');
    }
}
