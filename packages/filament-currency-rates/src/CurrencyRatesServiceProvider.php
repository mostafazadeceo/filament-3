<?php

namespace Haida\FilamentCurrencyRates;

use Haida\FilamentCurrencyRates\Jobs\AutoSyncCurrencyRatesJob;
use Haida\FilamentCurrencyRates\Services\CurrencyRateApiProvider;
use Haida\FilamentCurrencyRates\Services\CurrencyRateManager;
use Haida\FilamentCurrencyRates\Services\CurrencyRateScraper;
use Haida\FilamentCurrencyRates\Services\CurrencyRateSyncService;
use Haida\FilamentCurrencyRates\Settings\CurrencyRateSettings;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Schema;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Throwable;

class CurrencyRatesServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-currency-rates')
            ->hasConfigFile('currency-rates')
            ->hasRoutes('api')
            ->hasMigrations([
                '2025_01_01_000001_create_currency_rates_table',
                '2025_01_01_000002_create_currency_rate_runs_table',
            ])
            ->runsMigrations();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(CurrencyRateScraper::class);
        $this->app->singleton(CurrencyRateApiProvider::class);
        $this->app->singleton(CurrencyRateSyncService::class);
        $this->app->singleton(CurrencyRateManager::class);
    }

    public function packageBooted(): void
    {
        $this->app->booted(function () {
            if (! Schema::hasTable('settings')) {
                return;
            }

            if (! config('currency-rates.schedule.enabled', true)) {
                return;
            }

            try {
                $settings = app(CurrencyRateSettings::class);
            } catch (Throwable) {
                return;
            }

            if (! $settings->enabled) {
                return;
            }

            $schedule = app(Schedule::class);
            $minutes = max(5, min(60, (int) $settings->interval_minutes));
            $expression = "*/{$minutes} * * * *";

            $schedule->call([CurrencyRateScheduler::class, 'sync'])->cron($expression);

            AutoSyncCurrencyRatesJob::ensureScheduled($minutes);
        });

        if (! app()->runningInConsole()) {
            $this->app->terminating(function () {
                try {
                    app(CurrencyRateSyncService::class)->syncIfStale();
                } catch (Throwable) {
                    // Skip to avoid breaking HTTP responses.
                }
            });
        }
    }
}
