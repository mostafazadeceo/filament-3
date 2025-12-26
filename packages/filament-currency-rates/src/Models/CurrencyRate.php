<?php

namespace Haida\FilamentCurrencyRates\Models;

use Illuminate\Database\Eloquent\Model;

class CurrencyRate extends Model
{
    protected $table = 'currency_rates';

    protected $fillable = [
        'code',
        'name',
        'buy_price',
        'sell_price',
        'source',
        'fetched_at',
        'raw_payload',
    ];

    protected function casts(): array
    {
        return [
            'buy_price' => 'decimal:2',
            'sell_price' => 'decimal:2',
            'fetched_at' => 'datetime',
            'raw_payload' => 'array',
        ];
    }
}
