<?php

namespace Haida\FilamentCurrencyRates\Support;

class CurrencyRateLabels
{
    public static function currencyLabel(string $code): string
    {
        $code = strtolower($code);
        $map = (array) config('currency-rates.currencies', []);

        return $map[$code] ?? strtoupper($code);
    }

    public static function sourceLabel(?string $value): string
    {
        return match ($value) {
            'alanchand' => 'الان‌چند',
            'custom_api' => 'ای‌پی‌آی سفارشی',
            default => (string) ($value ?? ''),
        };
    }

    public static function statusLabel(?string $value): string
    {
        return match ($value) {
            'success' => 'موفق',
            'failed' => 'ناموفق',
            default => (string) ($value ?? ''),
        };
    }
}
