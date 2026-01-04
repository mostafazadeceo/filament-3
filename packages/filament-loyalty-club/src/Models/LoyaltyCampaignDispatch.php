<?php

namespace Haida\FilamentLoyaltyClub\Models;

use Haida\FilamentLoyaltyClub\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyCampaignDispatch extends Model
{
    use UsesTenant;

    protected $table = 'loyalty_campaign_dispatches';

    protected $fillable = [
        'tenant_id',
        'campaign_id',
        'variant_id',
        'customer_id',
        'channel',
        'status',
        'dispatched_at',
        'delivered_at',
        'opened_at',
        'clicked_at',
        'converted_at',
        'conversion_event_id',
        'meta',
    ];

    protected $casts = [
        'dispatched_at' => 'datetime',
        'delivered_at' => 'datetime',
        'opened_at' => 'datetime',
        'clicked_at' => 'datetime',
        'converted_at' => 'datetime',
        'meta' => 'array',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(LoyaltyCampaign::class, 'campaign_id');
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(LoyaltyCampaignVariant::class, 'variant_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(LoyaltyCustomer::class, 'customer_id');
    }
}
