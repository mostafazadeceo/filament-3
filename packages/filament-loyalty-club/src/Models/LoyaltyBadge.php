<?php

namespace Haida\FilamentLoyaltyClub\Models;

use Haida\FilamentLoyaltyClub\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoyaltyBadge extends Model
{
    use UsesTenant;

    protected $table = 'loyalty_badges';

    protected $fillable = [
        'tenant_id',
        'name',
        'icon',
        'description',
        'status',
        'perks',
    ];

    protected $casts = [
        'perks' => 'array',
    ];

    public function missions(): HasMany
    {
        return $this->hasMany(LoyaltyMission::class, 'badge_id');
    }
}
