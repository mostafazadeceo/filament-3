<?php

namespace Haida\FilamentCurrencyRates\Http\Controllers;

use Haida\FilamentCurrencyRates\Models\CurrencyRate;
use Haida\FilamentCurrencyRates\Services\CurrencyRateManager;
use Haida\FilamentCurrencyRates\Settings\CurrencyRateSettings;
use Haida\FilamentCurrencyRates\Support\CurrencyUnit;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CurrencyRateApiController
{
    public function index(Request $request): Response
    {
        $settings = app(CurrencyRateSettings::class);
        if (! $settings->api_enabled) {
            return response(['message' => 'سرویس غیرفعال است.'], 404);
        }

        if (! $this->tokenValid($request, $settings)) {
            return response(['message' => 'دسترسی غیرمجاز.'], 403);
        }

        $codes = $this->normalizeCodes($request->query('codes'));

        $query = CurrencyRate::query();
        if ($codes) {
            $query->whereIn('code', array_map('strtoupper', $codes));
        }

        $rates = $query->get();
        $displayUnit = CurrencyUnit::normalize($settings->display_unit ?? CurrencyUnit::IRR);
        $manager = app(CurrencyRateManager::class);

        $data = [];
        foreach ($rates as $rate) {
            $code = strtolower($rate->code);
            $buyRawIrr = $rate->buy_price !== null ? (float) $rate->buy_price : null;
            $sellRawIrr = $rate->sell_price !== null ? (float) $rate->sell_price : null;
            $buyRawDisplay = CurrencyUnit::fromIrr($buyRawIrr, $displayUnit);
            $sellRawDisplay = CurrencyUnit::fromIrr($sellRawIrr, $displayUnit);
            $buyFinal = $manager->getBuyPrice($code, $displayUnit);
            $sellFinal = $manager->getSellPrice($code, $displayUnit);
            $effectiveFinal = $manager->getEffectiveRate($code, $displayUnit);
            $effectiveIrr = $manager->applyProfit($manager->getBaseRateIrr($code));

            $data[$code] = [
                'code' => $code,
                'name' => $rate->name,
                'buy' => $buyFinal,
                'sell' => $sellFinal,
                'effective' => $effectiveFinal,
                'unit' => CurrencyUnit::label($displayUnit),
                'unit_code' => $displayUnit,
                'buy_raw' => $buyRawDisplay,
                'sell_raw' => $sellRawDisplay,
                'buy_irr' => $buyRawIrr,
                'sell_irr' => $sellRawIrr,
                'effective_irr' => $effectiveIrr,
                'base_rate' => $settings->base_rate ?? 'sell',
                'profit' => [
                    'enabled' => (bool) ($settings->profit_enabled ?? false),
                    'percent' => (float) ($settings->profit_percent ?? 0),
                    'fixed_amount' => (float) ($settings->profit_fixed_amount ?? 0),
                    'fixed_unit' => CurrencyUnit::normalize($settings->profit_fixed_unit ?? CurrencyUnit::IRR),
                ],
                'source' => $rate->source,
                'fetched_at' => optional($rate->fetched_at)->toIso8601String(),
                'updated_at' => optional($rate->updated_at)->toIso8601String(),
            ];
        }

        return response(['data' => $data], 200);
    }

    public function show(Request $request, string $code): Response
    {
        $settings = app(CurrencyRateSettings::class);
        if (! $settings->api_enabled) {
            return response(['message' => 'سرویس غیرفعال است.'], 404);
        }

        if (! $this->tokenValid($request, $settings)) {
            return response(['message' => 'دسترسی غیرمجاز.'], 403);
        }

        $rate = CurrencyRate::query()->where('code', strtoupper($code))->first();
        if (! $rate) {
            return response(['message' => 'یافت نشد.'], 404);
        }
        $displayUnit = CurrencyUnit::normalize($settings->display_unit ?? CurrencyUnit::IRR);
        $manager = app(CurrencyRateManager::class);
        $codeNormalized = strtolower($rate->code);
        $buyRawIrr = $rate->buy_price !== null ? (float) $rate->buy_price : null;
        $sellRawIrr = $rate->sell_price !== null ? (float) $rate->sell_price : null;
        $buyRawDisplay = CurrencyUnit::fromIrr($buyRawIrr, $displayUnit);
        $sellRawDisplay = CurrencyUnit::fromIrr($sellRawIrr, $displayUnit);
        $buyFinal = $manager->getBuyPrice($codeNormalized, $displayUnit);
        $sellFinal = $manager->getSellPrice($codeNormalized, $displayUnit);
        $effectiveFinal = $manager->getEffectiveRate($codeNormalized, $displayUnit);
        $effectiveIrr = $manager->applyProfit($manager->getBaseRateIrr($codeNormalized));

        return response([
            'data' => [
                'code' => $codeNormalized,
                'name' => $rate->name,
                'buy' => $buyFinal,
                'sell' => $sellFinal,
                'effective' => $effectiveFinal,
                'unit' => CurrencyUnit::label($displayUnit),
                'unit_code' => $displayUnit,
                'buy_raw' => $buyRawDisplay,
                'sell_raw' => $sellRawDisplay,
                'buy_irr' => $buyRawIrr,
                'sell_irr' => $sellRawIrr,
                'effective_irr' => $effectiveIrr,
                'base_rate' => $settings->base_rate ?? 'sell',
                'profit' => [
                    'enabled' => (bool) ($settings->profit_enabled ?? false),
                    'percent' => (float) ($settings->profit_percent ?? 0),
                    'fixed_amount' => (float) ($settings->profit_fixed_amount ?? 0),
                    'fixed_unit' => CurrencyUnit::normalize($settings->profit_fixed_unit ?? CurrencyUnit::IRR),
                ],
                'source' => $rate->source,
                'fetched_at' => optional($rate->fetched_at)->toIso8601String(),
                'updated_at' => optional($rate->updated_at)->toIso8601String(),
            ],
        ], 200);
    }

    protected function tokenValid(Request $request, CurrencyRateSettings $settings): bool
    {
        $token = $settings->api_token ?: config('currency-rates.api.token');
        if (blank($token)) {
            return false;
        }

        $headerName = (string) config('currency-rates.api.token_header', 'X-Rate-Token');
        $provided = $request->header($headerName) ?: $request->query('token');

        return is_string($provided) && hash_equals($token, $provided);
    }

    protected function normalizeCodes(mixed $input): array
    {
        if (is_array($input)) {
            return array_values(array_filter(array_map('strtolower', $input)));
        }

        if (is_string($input)) {
            return array_values(array_filter(array_map('strtolower', explode(',', $input))));
        }

        return [];
    }
}
