<?php

namespace Haida\FilamentLoyaltyClub\Models;

use Haida\FilamentLoyaltyClub\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoyaltyCampaign extends Model
{
    use UsesTenant;

    protected $table = 'loyalty_campaigns';

    protected $fillable = [
        'tenant_id',
        'name',
        'status',
        'channels',
        'segment_strategy',
        'schedule_start_at',
        'schedule_end_at',
        'created_by',
        'meta',
    ];

    protected $casts = [
        'channels' => 'array',
        'schedule_start_at' => 'datetime',
        'schedule_end_at' => 'datetime',
        'meta' => 'array',
    ];

    public function segments(): BelongsToMany
    {
        return $this->belongsToMany(LoyaltySegment::class, 'loyalty_campaign_segments', 'campaign_id', 'segment_id')
            ->withTimestamps();
    }

    public function variants(): HasMany
    {
        return $this->hasMany(LoyaltyCampaignVariant::class, 'campaign_id');
    }

    public function dispatches(): HasMany
    {
        return $this->hasMany(LoyaltyCampaignDispatch::class, 'campaign_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'created_by');
    }
}
