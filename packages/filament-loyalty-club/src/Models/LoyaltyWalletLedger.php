<?php

namespace Haida\FilamentLoyaltyClub\Models;

use Haida\FilamentLoyaltyClub\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyWalletLedger extends Model
{
    use UsesTenant;

    protected $table = 'loyalty_wallet_ledgers';

    protected $fillable = [
        'tenant_id',
        'customer_id',
        'event_id',
        'type',
        'points_delta',
        'cashback_delta',
        'balance_after_points',
        'balance_after_cashback',
        'status',
        'idempotency_key',
        'reference_type',
        'reference_id',
        'reversal_of_id',
        'meta',
        'expires_at',
    ];

    protected $casts = [
        'points_delta' => 'integer',
        'cashback_delta' => 'decimal:4',
        'balance_after_points' => 'integer',
        'balance_after_cashback' => 'decimal:4',
        'meta' => 'array',
        'expires_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(LoyaltyCustomer::class, 'customer_id');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(LoyaltyEvent::class, 'event_id');
    }
}
