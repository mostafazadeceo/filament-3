<?php

namespace Haida\FilamentLoyaltyClub\Models;

use Haida\FilamentLoyaltyClub\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyEvent extends Model
{
    use UsesTenant;

    protected $table = 'loyalty_events';

    protected $fillable = [
        'tenant_id',
        'customer_id',
        'type',
        'source',
        'idempotency_key',
        'status',
        'payload',
        'occurred_at',
        'processed_at',
        'error_message',
    ];

    protected $casts = [
        'payload' => 'array',
        'occurred_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(LoyaltyCustomer::class, 'customer_id');
    }
}
