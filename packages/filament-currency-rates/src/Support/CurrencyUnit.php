<?php

namespace Haida\FilamentCurrencyRates\Support;

class CurrencyUnit
{
    public const IRR = 'irr';

    public const IRT = 'irt';

    public static function normalize(?string $unit): string
    {
        $unit = strtolower((string) $unit);

        return in_array($unit, [self::IRR, self::IRT], true) ? $unit : self::IRR;
    }

    public static function toIrr(?float $value, ?string $unit): ?float
    {
        if ($value === null) {
            return null;
        }

        $unit = self::normalize($unit);

        return $unit === self::IRT ? $value * 10 : $value;
    }

    public static function fromIrr(?float $value, ?string $unit): ?float
    {
        if ($value === null) {
            return null;
        }

        $unit = self::normalize($unit);

        return $unit === self::IRT ? $value / 10 : $value;
    }

    public static function label(?string $unit): string
    {
        return match (self::normalize($unit)) {
            self::IRT => 'تومان',
            default => 'ریال',
        };
    }

    public static function format(?float $value, ?string $unit, int $decimals = 0): string
    {
        if ($value === null) {
            return '-';
        }

        $converted = self::fromIrr($value, $unit);

        return number_format((float) $converted, $decimals);
    }
}
