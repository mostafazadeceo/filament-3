<?php

namespace Haida\FilamentCurrencyRates\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Haida\FilamentCurrencyRates\Services\CurrencyRateManager;
use Haida\FilamentCurrencyRates\Settings\CurrencyRateSettings;
use Haida\FilamentCurrencyRates\Support\CurrencyRateLabels;
use Haida\FilamentCurrencyRates\Support\CurrencyUnit;
use Throwable;

class CurrencyRatesWidget extends StatsOverviewWidget
{
    protected ?string $heading = 'نرخ ارزهای کلیدی';

    public static function canView(): bool
    {
        try {
            return app(CurrencyRateSettings::class)->enabled;
        } catch (Throwable) {
            return false;
        }
    }

    protected function getStats(): array
    {
        $settings = app(CurrencyRateSettings::class);
        $codes = array_map('strtolower', $settings->currencies);
        $manager = app(CurrencyRateManager::class);
        $unit = $manager->displayUnit();

        $stats = [];
        foreach ($codes as $code) {
            $rate = $manager->getEffectiveRate($code, $unit);
            $value = $rate !== null ? number_format($rate, 0).' '.CurrencyUnit::label($unit) : '-';

            $stats[] = Stat::make(CurrencyRateLabels::currencyLabel($code), $value)
                ->description('نرخ نهایی')
                ->icon('heroicon-o-currency-dollar');
        }

        return $stats;
    }
}
