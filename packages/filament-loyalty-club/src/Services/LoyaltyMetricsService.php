<?php

namespace Haida\FilamentLoyaltyClub\Services;

use Carbon\CarbonImmutable;
use Haida\FilamentLoyaltyClub\Models\LoyaltyCustomer;
use Haida\FilamentLoyaltyClub\Models\LoyaltyCustomerMetric;

class LoyaltyMetricsService
{
    public function recordPurchase(LoyaltyCustomer $customer, float $amount, ?CarbonImmutable $occurredAt = null): LoyaltyCustomerMetric
    {
        $occurredAt = $occurredAt ?: CarbonImmutable::now();

        $metric = LoyaltyCustomerMetric::query()->firstOrCreate([
            'tenant_id' => $customer->tenant_id,
            'customer_id' => $customer->getKey(),
        ], [
            'purchase_count' => 0,
            'monetary_total' => 0,
        ]);

        $metric->purchase_count += 1;
        $metric->monetary_total += $amount;
        $metric->last_purchase_at = $occurredAt;
        $metric->recency_days = $occurredAt->diffInDays(CarbonImmutable::now());
        $recencyScore = $this->scoreRecency((int) $metric->recency_days);
        $metric->frequency_score = $this->scoreFrequency($metric->purchase_count);
        $metric->monetary_score = $this->scoreMonetary((float) $metric->monetary_total);
        $metric->rfm_score = (int) (($recencyScore * 100) + (($metric->frequency_score ?? 0) * 10) + ($metric->monetary_score ?? 0));
        $metric->save();

        return $metric;
    }

    protected function scoreFrequency(int $count): int
    {
        $thresholds = (array) config('filament-loyalty-club.segments.rfm.frequency_thresholds', [1, 3, 5]);
        $score = 1;
        foreach ($thresholds as $index => $threshold) {
            if ($count >= (int) $threshold) {
                $score = $index + 1;
            }
        }

        return $score;
    }

    protected function scoreMonetary(float $amount): int
    {
        $thresholds = (array) config('filament-loyalty-club.segments.rfm.monetary_thresholds', [1000000, 5000000, 20000000]);
        $score = 1;
        foreach ($thresholds as $index => $threshold) {
            if ($amount >= (float) $threshold) {
                $score = $index + 1;
            }
        }

        return $score;
    }

    protected function scoreRecency(int $days): int
    {
        $thresholds = (array) config('filament-loyalty-club.segments.rfm.recency_days', [30, 90, 180]);
        $score = count($thresholds) + 1;
        foreach ($thresholds as $index => $threshold) {
            if ($days <= (int) $threshold) {
                $score = $index + 1;
                break;
            }
        }

        return $score;
    }
}
