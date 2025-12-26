<?php

namespace Haida\FilamentCurrencyRates;

use Haida\FilamentCurrencyRates\Jobs\SyncCurrencyRatesJob;

class CurrencyRateScheduler
{
    public function sync(): void
    {
        SyncCurrencyRatesJob::dispatch();
    }
}
