<?php

namespace Haida\FilamentCurrencyRates\Settings;

use Haida\FilamentCurrencyRates\Support\EncryptedCast;
use Spatie\LaravelSettings\Settings;

class CurrencyRateSettings extends Settings
{
    public bool $enabled = true;

    public string $source = 'alanchand';

    public string $scrape_url = 'https://alanchand.com/';

    public int $interval_minutes = 30;

    public string $source_unit = 'irt';

    public string $display_unit = 'irt';

    public string $base_rate = 'sell';

    public bool $profit_enabled = false;

    public float $profit_percent = 0;

    public float $profit_fixed_amount = 0;

    public string $profit_fixed_unit = 'irt';

    public array $currencies = ['usd', 'eur', 'gbp', 'aed', 'cny'];

    public int $timeout = 30;

    public int $retry_times = 2;

    public int $retry_sleep_ms = 500;

    public bool $cache_enabled = true;

    public int $cache_ttl_seconds = 1200;

    public bool $api_enabled = true;

    public ?string $api_token = null;

    public ?string $custom_api_url = null;

    public ?string $custom_api_token = null;

    public static function group(): string
    {
        return 'currency_rates';
    }

    public static function casts(): array
    {
        return [
            'api_token' => EncryptedCast::class,
            'custom_api_token' => EncryptedCast::class,
        ];
    }
}
