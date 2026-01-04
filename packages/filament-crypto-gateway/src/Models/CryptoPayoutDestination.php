<?php

namespace Haida\FilamentCryptoGateway\Models;

use Haida\FilamentCryptoCore\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;

class CryptoPayoutDestination extends Model
{
    use UsesTenant;

    protected $fillable = [
        'tenant_id',
        'label',
        'address',
        'currency',
        'network',
        'status',
        'approved_at',
        'approved_by',
        'last_used_at',
        'meta',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'last_used_at' => 'datetime',
        'meta' => 'array',
    ];

    public function getTable(): string
    {
        return config('filament-crypto-gateway.tables.payout_destinations', 'crypto_payout_destinations');
    }
}
