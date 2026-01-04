<?php

namespace Haida\FilamentLoyaltyClub\Models;

use Haida\FilamentLoyaltyClub\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoyaltyWalletAccount extends Model
{
    use UsesTenant;

    protected $table = 'loyalty_wallet_accounts';

    protected $fillable = [
        'tenant_id',
        'customer_id',
        'points_balance',
        'points_earned_total',
        'points_redeemed_total',
        'cashback_balance',
        'cashback_earned_total',
        'cashback_redeemed_total',
    ];

    protected $casts = [
        'points_balance' => 'integer',
        'points_earned_total' => 'integer',
        'points_redeemed_total' => 'integer',
        'cashback_balance' => 'decimal:4',
        'cashback_earned_total' => 'decimal:4',
        'cashback_redeemed_total' => 'decimal:4',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(LoyaltyCustomer::class, 'customer_id');
    }

    public function ledgers(): HasMany
    {
        return $this->hasMany(LoyaltyWalletLedger::class, 'customer_id', 'customer_id');
    }
}
