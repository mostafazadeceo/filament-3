<?php

namespace Haida\FilamentCurrencyRates\Services;

use Haida\FilamentCurrencyRates\Models\CurrencyRate;
use Haida\FilamentCurrencyRates\Models\CurrencyRateRun;
use Haida\FilamentCurrencyRates\Settings\CurrencyRateSettings;
use Haida\FilamentCurrencyRates\Support\CurrencyRateLabels;
use Haida\FilamentCurrencyRates\Support\CurrencyUnit;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Throwable;

class CurrencyRateSyncService
{
    public function __construct(
        protected CurrencyRateScraper $scraper,
        protected CurrencyRateApiProvider $apiProvider,
    ) {}

    public function sync(): int
    {
        $settings = app(CurrencyRateSettings::class);

        if (! $settings->enabled) {
            return 0;
        }

        $startedAt = microtime(true);
        $source = $settings->source ?: 'alanchand';
        $rates = [];

        try {
            $rates = $this->fetchRates($settings, $source);
            $rates = $this->filterRates($rates, $settings->currencies);

            if (! $rates) {
                throw new RuntimeException('هیچ نرخی از منبع دریافت نشد.');
            }

            foreach ($rates as $code => $payload) {
                $label = $payload['name'] ?? CurrencyRateLabels::currencyLabel($code);
                $buy = CurrencyUnit::toIrr($payload['buy'] ?? null, $settings->source_unit);
                $sell = CurrencyUnit::toIrr($payload['sell'] ?? null, $settings->source_unit);

                CurrencyRate::query()->updateOrCreate([
                    'code' => strtoupper($code),
                ], [
                    'name' => $label,
                    'buy_price' => $buy,
                    'sell_price' => $sell,
                    'source' => $source,
                    'fetched_at' => now(),
                    'raw_payload' => $payload,
                ]);
            }

            CurrencyRateRun::query()->create([
                'source' => $source,
                'status' => 'success',
                'rates_count' => count($rates),
                'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
                'fetched_at' => now(),
                'payload' => [
                    'codes' => array_keys($rates),
                ],
            ]);

            return count($rates);
        } catch (Throwable $exception) {
            CurrencyRateRun::query()->create([
                'source' => $source,
                'status' => 'failed',
                'rates_count' => count($rates),
                'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
                'fetched_at' => now(),
                'error_message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    public function syncIfStale(bool $ignoreHttpFallback = false): void
    {
        if (! $ignoreHttpFallback && ! config('currency-rates.schedule.http_fallback', true)) {
            return;
        }

        if (! Schema::hasTable('currency_rate_runs')) {
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
        $lastRun = CurrencyRateRun::query()->latest('fetched_at')->value('fetched_at');
        if ($lastRun) {
            $last = $lastRun instanceof Carbon ? $lastRun : Carbon::parse($lastRun);
            if ($last->diffInMinutes(now()) < $interval) {
                return;
            }
        }

        $lockKey = 'currency_rates:auto_sync';
        $lockTtl = max(60, $interval * 60);
        if (! Cache::add($lockKey, true, $lockTtl)) {
            return;
        }

        try {
            $this->sync();
        } catch (Throwable $exception) {
            report($exception);
        } finally {
            Cache::forget($lockKey);
        }
    }

    protected function fetchRates(CurrencyRateSettings $settings, string $source): array
    {
        $cacheKey = 'currency_rates:'.$source;
        $ttl = max(60, (int) $settings->cache_ttl_seconds);

        if ($settings->cache_enabled && Cache::has($cacheKey)) {
            return (array) Cache::get($cacheKey, []);
        }

        $rates = match ($source) {
            'custom_api' => $this->apiProvider->fetch(
                $settings->custom_api_url,
                $settings->custom_api_token,
                $settings->timeout,
                $settings->retry_times,
                $settings->retry_sleep_ms,
                $settings->currencies,
            ),
            default => $this->scraper->fetch(
                $settings->scrape_url,
                $settings->timeout,
                $settings->retry_times,
                $settings->retry_sleep_ms,
                config('currency-rates.sources.alanchand.user_agent'),
            ),
        };

        if ($settings->cache_enabled) {
            Cache::put($cacheKey, $rates, $ttl);
        }

        return $rates;
    }

    protected function filterRates(array $rates, array $currencies): array
    {
        $filtered = [];
        foreach ($currencies as $code) {
            $key = strtolower($code);
            if (! array_key_exists($key, $rates)) {
                continue;
            }

            $filtered[$key] = $rates[$key];
        }

        return $filtered;
    }
}
