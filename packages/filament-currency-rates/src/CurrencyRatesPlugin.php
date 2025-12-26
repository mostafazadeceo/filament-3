<?php

namespace Haida\FilamentCurrencyRates;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Haida\FilamentCurrencyRates\Pages\CurrencyRateSettingsPage;
use Haida\FilamentCurrencyRates\Resources\CurrencyRateResource;
use Haida\FilamentCurrencyRates\Resources\CurrencyRateRunResource;
use Haida\FilamentCurrencyRates\Widgets\CurrencyRatesWidget;

class CurrencyRatesPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'currency-rates';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->pages([
                CurrencyRateSettingsPage::class,
            ])
            ->resources([
                CurrencyRateResource::class,
                CurrencyRateRunResource::class,
            ])
            ->widgets([
                CurrencyRatesWidget::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        // Plugin boot hook.
    }
}
