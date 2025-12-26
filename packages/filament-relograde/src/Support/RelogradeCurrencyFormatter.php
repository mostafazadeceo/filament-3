<?php

namespace Haida\FilamentRelograde\Support;

use Haida\FilamentCurrencyRates\Services\CurrencyRateManager;
use Haida\FilamentCurrencyRates\Support\CurrencyUnit;

class RelogradeCurrencyFormatter
{
    public static function formatAmount(?float $amount, ?string $currency, bool $showCurrency = true, int $rawDecimals = 4, int $equivalentDecimals = 0): string
    {
        if ($amount === null || ! is_numeric($amount)) {
            return '-';
        }

        $raw = number_format((float) $amount, $rawDecimals);
        $currencyCode = strtoupper((string) $currency);
        if ($showCurrency && $currencyCode !== '') {
            $raw .= ' '.$currencyCode;
        }

        $equivalent = self::formatEquivalent((float) $amount, $currency, $equivalentDecimals);
        if (! $equivalent) {
            return $raw;
        }

        return $raw.' (معادل '.$equivalent.')';
    }

    public static function formatEquivalent(?float $amount, ?string $currency, int $decimals = 0): ?string
    {
        if ($amount === null || ! is_numeric($amount)) {
            return null;
        }

        $currency = strtolower(trim((string) $currency));
        if ($currency === '') {
            return null;
        }

        if (! class_exists(CurrencyRateManager::class)) {
            return null;
        }

        $manager = app(CurrencyRateManager::class);
        $converted = $manager->convertToDisplay((float) $amount, $currency);
        if ($converted === null) {
            return null;
        }

        $unit = $manager->displayUnit();

        return number_format($converted, $decimals).' '.CurrencyUnit::label($unit);
    }
}
