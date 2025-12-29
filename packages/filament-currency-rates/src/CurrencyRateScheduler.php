<?php

namespace Haida\FilamentCurrencyRates;

use Haida\FilamentCurrencyRates\Jobs\SyncCurrencyRatesJob;

class CurrencyRateScheduler
{
    public static function sync(): void
    {
        SyncCurrencyRatesJob::dispatch();
    }
}
