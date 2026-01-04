<?php

namespace Haida\FilamentCryptoCore\Models;

use Illuminate\Database\Eloquent\Model;

class CryptoRate extends Model
{
    protected $fillable = [
        'from',
        'to',
        'rate',
        'source',
        'quoted_at',
    ];

    protected $casts = [
        'rate' => 'decimal:8',
        'quoted_at' => 'datetime',
    ];

    public function getTable(): string
    {
        return config('filament-crypto-core.tables.rates', 'crypto_rates');
    }
}
