<?php

namespace Haida\FilamentLoyaltyClub\Models;

use Haida\FilamentLoyaltyClub\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoyaltyMission extends Model
{
    use UsesTenant;

    protected $table = 'loyalty_missions';

    protected $fillable = [
        'tenant_id',
        'badge_id',
        'name',
        'description',
        'type',
        'status',
        'criteria',
        'reward_points',
        'reward_cashback',
        'start_at',
        'end_at',
    ];

    protected $casts = [
        'criteria' => 'array',
        'reward_points' => 'integer',
        'reward_cashback' => 'decimal:4',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function badge(): BelongsTo
    {
        return $this->belongsTo(LoyaltyBadge::class, 'badge_id');
    }

    public function progress(): HasMany
    {
        return $this->hasMany(LoyaltyMissionProgress::class, 'mission_id');
    }
}
