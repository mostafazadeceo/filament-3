<?php

namespace Haida\FilamentCurrencyRates\Models;

use Illuminate\Database\Eloquent\Model;

class CurrencyRateRun extends Model
{
    protected $table = 'currency_rate_runs';

    protected $fillable = [
        'source',
        'status',
        'rates_count',
        'duration_ms',
        'fetched_at',
        'error_message',
        'payload',
    ];

    protected function casts(): array
    {
        return [
            'rates_count' => 'integer',
            'duration_ms' => 'integer',
            'fetched_at' => 'datetime',
            'payload' => 'array',
        ];
    }
}
