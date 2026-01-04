<?php

namespace Haida\CommerceCatalog\Services;

use Haida\FilamentCurrencyRates\Models\CurrencyRate;

class CatalogPricingService
{
    public function convert(float $amount, string $from, string $to): ?float
    {
        $from = strtoupper($from);
        $to = strtoupper($to);

        if ($from === $to) {
            return $amount;
        }

        $irr = 'IRR';

        if ($from !== $irr) {
            $fromRate = $this->rateFor($from);
            if (! $fromRate) {
                return null;
            }
            $amount = $amount * $fromRate;
        }

        if ($to === $irr) {
            return $amount;
        }

        $toRate = $this->rateFor($to);
        if (! $toRate) {
            return null;
        }

        return $amount / $toRate;
    }

    private function rateFor(string $code): ?float
    {
        $rate = CurrencyRate::query()
            ->where('code', strtoupper($code))
            ->value('sell_price');

        if ($rate === null) {
            return null;
        }

        return (float) $rate;
    }
}
