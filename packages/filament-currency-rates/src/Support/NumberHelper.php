<?php

namespace Haida\FilamentCurrencyRates\Support;

class NumberHelper
{
    public static function normalize(string $value): ?float
    {
        $clean = trim(strip_tags($value));
        if ($clean === '') {
            return null;
        }

        $map = [
            '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
            '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
            '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
            '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
            '٬' => '', ',' => '', ' ' => '', '\u{00A0}' => '',
            '٫' => '.', '٬' => '',
        ];

        $clean = strtr($clean, $map);
        $clean = preg_replace('/[^0-9.\-]/', '', $clean);

        if ($clean === '' || $clean === '-' || $clean === '.') {
            return null;
        }

        return (float) $clean;
    }
}
