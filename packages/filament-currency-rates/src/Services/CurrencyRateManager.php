<?php

namespace Haida\FilamentCurrencyRates\Services;

use Haida\FilamentCurrencyRates\Models\CurrencyRate;
use Haida\FilamentCurrencyRates\Settings\CurrencyRateSettings;
use Haida\FilamentCurrencyRates\Support\CurrencyUnit;

class CurrencyRateManager
{
    public function getRate(string $code): ?CurrencyRate
    {
        return CurrencyRate::query()->where('code', strtoupper($code))->first();
    }

    public function getSellPrice(string $code, ?string $unit = null): ?float
    {
        $price = $this->getSellPriceRaw($code, CurrencyUnit::IRR);

        if ($price === null) {
            return null;
        }

        $final = $this->applyProfit($price);

        return CurrencyUnit::fromIrr($final, $unit ?? $this->displayUnit());
    }

    public function getSellPriceRaw(string $code, ?string $unit = null): ?float
    {
        $rate = $this->getRate($code);

        if (! $rate?->sell_price) {
            return null;
        }

        return CurrencyUnit::fromIrr((float) $rate->sell_price, $unit ?? CurrencyUnit::IRR);
    }

    public function getBuyPrice(string $code, ?string $unit = null): ?float
    {
        $price = $this->getBuyPriceRaw($code, CurrencyUnit::IRR);

        if ($price === null) {
            return null;
        }

        $final = $this->applyProfit($price);

        return CurrencyUnit::fromIrr($final, $unit ?? $this->displayUnit());
    }

    public function getBuyPriceRaw(string $code, ?string $unit = null): ?float
    {
        $rate = $this->getRate($code);

        if (! $rate?->buy_price) {
            return null;
        }

        return CurrencyUnit::fromIrr((float) $rate->buy_price, $unit ?? CurrencyUnit::IRR);
    }

    public function getEffectiveRate(string $code, ?string $unit = null): ?float
    {
        $base = $this->getBaseRateIrr($code);
        if ($base === null) {
            return null;
        }

        $final = $this->applyProfit($base);

        return CurrencyUnit::fromIrr($final, $unit ?? $this->displayUnit());
    }

    public function getBaseRateIrr(string $code): ?float
    {
        $rate = $this->getRate($code);
        if (! $rate) {
            return null;
        }

        $settings = app(CurrencyRateSettings::class);
        $base = match ($settings->base_rate ?? 'sell') {
            'buy' => $rate->buy_price,
            'average' => $this->averageRate($rate),
            default => $rate->sell_price,
        };

        if ($base === null) {
            $base = $rate->sell_price ?? $rate->buy_price;
        }

        return $base !== null ? (float) $base : null;
    }

    public function applyProfit(?float $value): ?float
    {
        if ($value === null) {
            return null;
        }

        $settings = app(CurrencyRateSettings::class);
        if (! ($settings->profit_enabled ?? false)) {
            return $value;
        }

        $percent = max(0, (float) ($settings->profit_percent ?? 0));
        $fixed = max(0, (float) ($settings->profit_fixed_amount ?? 0));
        $fixedIrr = CurrencyUnit::toIrr($fixed, $settings->profit_fixed_unit ?? CurrencyUnit::IRR) ?? 0;

        return $value + ($value * ($percent / 100)) + $fixedIrr;
    }

    public function convertToIrr(float $amount, string $code): ?float
    {
        $rate = $this->getEffectiveRate($code, CurrencyUnit::IRR);
        if (! $rate) {
            return null;
        }

        return $amount * $rate;
    }

    public function convertToIrt(float $amount, string $code): ?float
    {
        $irr = $this->convertToIrr($amount, $code);
        if ($irr === null) {
            return null;
        }

        return CurrencyUnit::fromIrr($irr, CurrencyUnit::IRT);
    }

    public function convertToDisplay(float $amount, string $code): ?float
    {
        $irr = $this->convertToIrr($amount, $code);
        if ($irr === null) {
            return null;
        }

        return CurrencyUnit::fromIrr($irr, $this->displayUnit());
    }

    public function convertFromIrr(float $amount, string $code): ?float
    {
        $rate = $this->getEffectiveRate($code, CurrencyUnit::IRR);
        if (! $rate || $rate == 0.0) {
            return null;
        }

        return $amount / $rate;
    }

    public function displayUnit(): string
    {
        return CurrencyUnit::normalize(app(CurrencyRateSettings::class)->display_unit ?? CurrencyUnit::IRR);
    }

    protected function averageRate(CurrencyRate $rate): ?float
    {
        if ($rate->buy_price !== null && $rate->sell_price !== null) {
            return ((float) $rate->buy_price + (float) $rate->sell_price) / 2;
        }

        return $rate->sell_price ?? $rate->buy_price;
    }
}
