<?php

namespace Haida\FilamentLoyaltyClub\Models;

use Haida\FilamentLoyaltyClub\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyPointsBucket extends Model
{
    use UsesTenant;

    protected $table = 'loyalty_points_buckets';

    protected $fillable = [
        'tenant_id',
        'customer_id',
        'ledger_id',
        'points_total',
        'points_available',
        'expires_at',
    ];

    protected $casts = [
        'points_total' => 'integer',
        'points_available' => 'integer',
        'expires_at' => 'datetime',
    ];

    public function ledger(): BelongsTo
    {
        return $this->belongsTo(LoyaltyWalletLedger::class, 'ledger_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(LoyaltyCustomer::class, 'customer_id');
    }
}
