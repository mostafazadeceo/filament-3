<?php

namespace Haida\FilamentLoyaltyClub\Models;

use Haida\FilamentLoyaltyClub\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoyaltySegment extends Model
{
    use UsesTenant;

    protected $table = 'loyalty_segments';

    protected $fillable = [
        'tenant_id',
        'name',
        'type',
        'status',
        'rules',
        'last_built_at',
        'meta',
    ];

    protected $casts = [
        'rules' => 'array',
        'last_built_at' => 'datetime',
        'meta' => 'array',
    ];

    public function customers(): BelongsToMany
    {
        return $this->belongsToMany(LoyaltyCustomer::class, 'loyalty_customer_segments', 'segment_id', 'customer_id')
            ->withPivot(['source', 'assigned_at'])
            ->withTimestamps();
    }

    public function campaigns(): BelongsToMany
    {
        return $this->belongsToMany(LoyaltyCampaign::class, 'loyalty_campaign_segments', 'segment_id', 'campaign_id')
            ->withTimestamps();
    }

    public function customerLinks(): HasMany
    {
        return $this->hasMany(LoyaltyCustomerSegment::class, 'segment_id');
    }
}
