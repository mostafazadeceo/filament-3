<?php

namespace Haida\FilamentLoyaltyClub\Models;

use Haida\FilamentLoyaltyClub\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyPointsConsumption extends Model
{
    use UsesTenant;

    protected $table = 'loyalty_points_consumptions';

    protected $fillable = [
        'tenant_id',
        'customer_id',
        'bucket_id',
        'ledger_id',
        'points_used',
    ];

    protected $casts = [
        'points_used' => 'integer',
    ];

    public function bucket(): BelongsTo
    {
        return $this->belongsTo(LoyaltyPointsBucket::class, 'bucket_id');
    }

    public function ledger(): BelongsTo
    {
        return $this->belongsTo(LoyaltyWalletLedger::class, 'ledger_id');
    }
}
