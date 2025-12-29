<?php

namespace Haida\FilamentCurrencyRates\Jobs;

use Haida\FilamentCurrencyRates\Services\CurrencyRateSyncService;
use Haida\FilamentCurrencyRates\Settings\CurrencyRateSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class AutoSyncCurrencyRatesJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(CurrencyRateSyncService $service): void
    {
        if (! config('currency-rates.schedule.enabled', true)) {
            return;
        }

        if (! Schema::hasTable('settings')) {
            return;
        }

        $settings = app(CurrencyRateSettings::class);
        if (! $settings->enabled) {
            return;
        }

        $interval = max(5, min(60, (int) $settings->interval_minutes));
        $service->syncIfStale(true);

        $this->scheduleNext($interval);
    }

    public static function ensureScheduled(int $intervalMinutes): void
    {
        if (! config('currency-rates.schedule.queue_auto', true)) {
            return;
        }

        if (self::isSyncDriver()) {
            return;
        }

        $ttl = max(60, $intervalMinutes * 60);
        if (! Cache::add(self::lockKey(), now()->timestamp, $ttl)) {
            return;
        }

        static::dispatch();
    }

    protected function scheduleNext(int $intervalMinutes): void
    {
        if (! config('currency-rates.schedule.queue_auto', true)) {
            return;
        }

        if (self::isSyncDriver()) {
            return;
        }

        $ttl = max(60, $intervalMinutes * 60);
        Cache::put(self::lockKey(), now()->timestamp, $ttl);

        static::dispatch()->delay(now()->addMinutes($intervalMinutes));
    }

    protected static function lockKey(): string
    {
        return 'currency_rates:auto_sync_job';
    }

    protected static function isSyncDriver(): bool
    {
        return config('queue.default') === 'sync';
    }
}
