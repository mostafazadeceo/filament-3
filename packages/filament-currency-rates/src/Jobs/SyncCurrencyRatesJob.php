<?php

namespace Haida\FilamentCurrencyRates\Jobs;

use Haida\FilamentCurrencyRates\Services\CurrencyRateSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncCurrencyRatesJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public bool $force = false,
    ) {}

    public function handle(CurrencyRateSyncService $service): void
    {
        if ($this->force) {
            $service->sync();

            return;
        }

        $service->syncIfStale(true);
    }
}
