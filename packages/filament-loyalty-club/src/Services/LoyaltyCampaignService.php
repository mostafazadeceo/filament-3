<?php

namespace Haida\FilamentLoyaltyClub\Services;

use Haida\FilamentLoyaltyClub\Models\LoyaltyCampaign;
use Haida\FilamentLoyaltyClub\Models\LoyaltyCampaignDispatch;
use Haida\FilamentLoyaltyClub\Models\LoyaltyCampaignVariant;
use Haida\FilamentLoyaltyClub\Models\LoyaltyCustomer;
use Haida\FilamentNotify\Core\Support\Triggers\TriggerDispatcher;

class LoyaltyCampaignService
{
    public function dispatchCampaign(LoyaltyCampaign $campaign): void
    {
        $panelId = (string) config('filament-loyalty-club.campaigns.panel', 'tenant');
        $eventName = (string) config('filament-loyalty-club.campaigns.dispatch_event', 'loyalty_campaign_dispatched');

        $segments = $campaign->segments()->pluck('loyalty_segments.id');
        if ($segments->isEmpty()) {
            return;
        }

        $segmentQuery = \Haida\FilamentLoyaltyClub\Models\LoyaltyCustomerSegment::query()
            ->where('tenant_id', $campaign->tenant_id)
            ->whereIn('segment_id', $segments);

        if ($campaign->segment_strategy === 'all') {
            $customerIds = $segmentQuery
                ->select('customer_id')
                ->groupBy('customer_id')
                ->havingRaw('count(*) = ?', [$segments->count()])
                ->pluck('customer_id');
        } else {
            $customerIds = $segmentQuery->pluck('customer_id')->unique();
        }

        foreach ($customerIds as $customerId) {
            $customer = LoyaltyCustomer::query()->find($customerId);
            if (! $customer) {
                continue;
            }

            $variant = $this->pickVariant($campaign);
            if (! $variant) {
                continue;
            }

            $dispatch = LoyaltyCampaignDispatch::query()->create([
                'tenant_id' => $campaign->tenant_id,
                'campaign_id' => $campaign->getKey(),
                'variant_id' => $variant->getKey(),
                'customer_id' => $customer->getKey(),
                'channel' => $variant->channel,
                'status' => 'pending',
            ]);

            if ($panelId !== '' && class_exists(TriggerDispatcher::class)) {
                try {
                    app(TriggerDispatcher::class)->dispatchForEloquent($panelId, $dispatch, $eventName);
                } catch (\Throwable) {
                    // Ignore dispatch failures to keep campaigns resilient.
                }
            }
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getOffersForCustomer(LoyaltyCustomer $customer): array
    {
        $campaigns = LoyaltyCampaign::query()
            ->where('tenant_id', $customer->tenant_id)
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('schedule_start_at')->orWhere('schedule_start_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('schedule_end_at')->orWhere('schedule_end_at', '>=', now());
            })
            ->get();

        $offers = [];

        foreach ($campaigns as $campaign) {
            $segments = $campaign->segments()->pluck('loyalty_segments.id');
            if ($segments->isNotEmpty()) {
                $segmentQuery = $customer->segments()->whereIn('loyalty_segments.id', $segments);
                if ($campaign->segment_strategy === 'all') {
                    $inSegment = $segmentQuery->count() === $segments->count();
                } else {
                    $inSegment = $segmentQuery->exists();
                }

                if (! $inSegment) {
                    continue;
                }
            }

            $variant = $this->pickVariant($campaign);
            if (! $variant) {
                continue;
            }

            $offers[] = [
                'campaign_id' => $campaign->getKey(),
                'variant_id' => $variant->getKey(),
                'channel' => $variant->channel,
                'content' => $variant->content,
            ];
        }

        return $offers;
    }

    protected function pickVariant(LoyaltyCampaign $campaign): ?LoyaltyCampaignVariant
    {
        $variants = $campaign->variants()->where('status', 'active')->get();
        if ($variants->isEmpty()) {
            return null;
        }

        $totalWeight = max(1, $variants->sum('weight'));
        $rand = random_int(1, $totalWeight);
        $running = 0;
        foreach ($variants as $variant) {
            $running += (int) $variant->weight;
            if ($rand <= $running) {
                return $variant;
            }
        }

        return $variants->first();
    }
}
