<?php

namespace Haida\FilamentLoyaltyClub\Models;

use Haida\FilamentLoyaltyClub\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoyaltyCampaignVariant extends Model
{
    use UsesTenant;

    protected $table = 'loyalty_campaign_variants';

    protected $fillable = [
        'tenant_id',
        'campaign_id',
        'name',
        'channel',
        'weight',
        'status',
        'content',
        'meta',
    ];

    protected $casts = [
        'weight' => 'integer',
        'content' => 'array',
        'meta' => 'array',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(LoyaltyCampaign::class, 'campaign_id');
    }

    public function dispatches(): HasMany
    {
        return $this->hasMany(LoyaltyCampaignDispatch::class, 'variant_id');
    }
}
