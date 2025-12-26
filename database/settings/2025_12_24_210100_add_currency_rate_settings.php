<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('currency_rates.enabled', true);
        $this->migrator->add('currency_rates.source', 'alanchand');
        $this->migrator->add('currency_rates.scrape_url', 'https://alanchand.com/');
        $this->migrator->add('currency_rates.interval_minutes', 30);
        $this->migrator->add('currency_rates.source_unit', 'irt');
        $this->migrator->add('currency_rates.display_unit', 'irt');
        $this->migrator->add('currency_rates.base_rate', 'sell');
        $this->migrator->add('currency_rates.profit_enabled', false);
        $this->migrator->add('currency_rates.profit_percent', 0);
        $this->migrator->add('currency_rates.profit_fixed_amount', 0);
        $this->migrator->add('currency_rates.profit_fixed_unit', 'irt');
        $this->migrator->add('currency_rates.currencies', ['usd', 'eur', 'gbp', 'aed', 'cny']);
        $this->migrator->add('currency_rates.timeout', 30);
        $this->migrator->add('currency_rates.retry_times', 2);
        $this->migrator->add('currency_rates.retry_sleep_ms', 500);
        $this->migrator->add('currency_rates.cache_enabled', true);
        $this->migrator->add('currency_rates.cache_ttl_seconds', 1200);
        $this->migrator->add('currency_rates.api_enabled', true);
        $this->migrator->add('currency_rates.api_token', null);
        $this->migrator->add('currency_rates.custom_api_url', null);
        $this->migrator->add('currency_rates.custom_api_token', null);
    }
};
