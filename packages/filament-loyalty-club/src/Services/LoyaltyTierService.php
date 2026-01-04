<?php

namespace Haida\FilamentLoyaltyClub\Services;

use Haida\FilamentLoyaltyClub\Models\LoyaltyCustomer;
use Haida\FilamentLoyaltyClub\Models\LoyaltyCustomerMetric;
use Haida\FilamentLoyaltyClub\Models\LoyaltyCustomerTier;
use Haida\FilamentLoyaltyClub\Models\LoyaltyTier;

class LoyaltyTierService
{
    public function __construct(protected LoyaltyAuditService $auditService) {}

    public function syncTier(LoyaltyCustomer $customer, array $context = []): bool
    {
        $account = app(LoyaltyLedgerService::class)->getOrCreateAccount($customer);
        $metrics = LoyaltyCustomerMetric::query()
            ->where('tenant_id', $customer->tenant_id)
            ->where('customer_id', $customer->getKey())
            ->first();

        $pointsTotal = (int) $account->points_earned_total;
        $spendTotal = (float) ($metrics?->monetary_total ?? ($context['purchase_amount'] ?? 0));

        $tiers = LoyaltyTier::query()
            ->where('tenant_id', $customer->tenant_id)
            ->where('is_active', true)
            ->orderBy('rank')
            ->get();

        if ($tiers->isEmpty()) {
            return false;
        }

        $selected = null;
        foreach ($tiers as $tier) {
            if ($pointsTotal >= (int) $tier->threshold_points && $spendTotal >= (float) $tier->threshold_spend) {
                $selected = $tier;
            }
        }

        if (! $selected) {
            $selected = $tiers->firstWhere('is_default', true) ?? $tiers->first();
        }

        if (! $selected || $customer->tier_id === $selected->getKey()) {
            return false;
        }

        $previousTier = $customer->tier_id;
        $customer->tier_id = $selected->getKey();
        $customer->save();

        $this->auditService->record('tier_changed', [
            'from_tier_id' => $previousTier,
            'to_tier_id' => $selected->getKey(),
        ], $customer);

        LoyaltyCustomerTier::query()
            ->where('tenant_id', $customer->tenant_id)
            ->where('customer_id', $customer->getKey())
            ->whereNull('ended_at')
            ->update(['ended_at' => now()]);

        LoyaltyCustomerTier::query()->create([
            'tenant_id' => $customer->tenant_id,
            'customer_id' => $customer->getKey(),
            'tier_id' => $selected->getKey(),
            'started_at' => now(),
            'reason' => 'auto',
        ]);

        return true;
    }
}
