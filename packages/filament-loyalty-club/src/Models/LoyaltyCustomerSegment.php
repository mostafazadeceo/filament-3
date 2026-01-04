<?php

namespace Haida\FilamentLoyaltyClub\Models;

use Haida\FilamentLoyaltyClub\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyCustomerSegment extends Model
{
    use UsesTenant;

    protected $table = 'loyalty_customer_segments';

    protected $fillable = [
        'tenant_id',
        'segment_id',
        'customer_id',
        'source',
        'assigned_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
    ];

    public function segment(): BelongsTo
    {
        return $this->belongsTo(LoyaltySegment::class, 'segment_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(LoyaltyCustomer::class, 'customer_id');
    }
}
