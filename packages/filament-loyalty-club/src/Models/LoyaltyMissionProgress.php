<?php

namespace Haida\FilamentLoyaltyClub\Models;

use Haida\FilamentLoyaltyClub\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyMissionProgress extends Model
{
    use UsesTenant;

    protected $table = 'loyalty_mission_progress';

    protected $fillable = [
        'tenant_id',
        'mission_id',
        'customer_id',
        'progress',
        'target',
        'status',
        'completed_at',
        'meta',
    ];

    protected $casts = [
        'progress' => 'integer',
        'target' => 'integer',
        'completed_at' => 'datetime',
        'meta' => 'array',
    ];

    public function mission(): BelongsTo
    {
        return $this->belongsTo(LoyaltyMission::class, 'mission_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(LoyaltyCustomer::class, 'customer_id');
    }
}
