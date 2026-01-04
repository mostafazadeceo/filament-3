<?php

namespace Haida\FilamentCryptoCore\Models;

use Illuminate\Database\Eloquent\Model;

class CryptoNetworkFee extends Model
{
    protected $fillable = [
        'currency',
        'network',
        'fee_model',
        'data',
        'quoted_at',
    ];

    protected $casts = [
        'data' => 'array',
        'quoted_at' => 'datetime',
    ];

    public function getTable(): string
    {
        return config('filament-crypto-core.tables.network_fees', 'crypto_network_fees');
    }
}
