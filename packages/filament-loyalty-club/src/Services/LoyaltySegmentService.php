<?php

namespace Haida\FilamentLoyaltyClub\Services;

use Haida\FilamentLoyaltyClub\Models\LoyaltyCustomer;
use Haida\FilamentLoyaltyClub\Models\LoyaltyCustomerMetric;
use Haida\FilamentLoyaltyClub\Models\LoyaltyCustomerSegment;
use Haida\FilamentLoyaltyClub\Models\LoyaltySegment;

class LoyaltySegmentService
{
    public function rebuildSegment(LoyaltySegment $segment): int
    {
        $rules = (array) ($segment->rules ?? []);
        $customerIds = [];

        if ($segment->type === 'rfm') {
            $query = LoyaltyCustomerMetric::query()->where('tenant_id', $segment->tenant_id);
            if (isset($rules['recency_days_max'])) {
                $query->where('recency_days', '<=', (int) $rules['recency_days_max']);
            }
            if (isset($rules['frequency_min'])) {
                $query->where('purchase_count', '>=', (int) $rules['frequency_min']);
            }
            if (isset($rules['monetary_min'])) {
                $query->where('monetary_total', '>=', (float) $rules['monetary_min']);
            }
            $customerIds = $query->pluck('customer_id')->toArray();
        } else {
            $query = LoyaltyCustomer::query()->where('tenant_id', $segment->tenant_id);
            if (isset($rules['status'])) {
                $query->where('status', (string) $rules['status']);
            }
            if (isset($rules['tier_id'])) {
                $query->where('tier_id', (int) $rules['tier_id']);
            }
            $customerIds = $query->pluck('id')->toArray();
        }

        LoyaltyCustomerSegment::query()
            ->where('tenant_id', $segment->tenant_id)
            ->where('segment_id', $segment->getKey())
            ->delete();

        foreach ($customerIds as $customerId) {
            LoyaltyCustomerSegment::query()->create([
                'tenant_id' => $segment->tenant_id,
                'segment_id' => $segment->getKey(),
                'customer_id' => $customerId,
                'source' => $segment->type,
                'assigned_at' => now(),
            ]);
        }

        $segment->last_built_at = now();
        $segment->save();

        return count($customerIds);
    }
}
