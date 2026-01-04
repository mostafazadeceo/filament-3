<?php

namespace Haida\FilamentLoyaltyClub\Services;

use Haida\FilamentLoyaltyClub\Models\LoyaltyBadgeAward;
use Haida\FilamentLoyaltyClub\Models\LoyaltyEvent;
use Haida\FilamentLoyaltyClub\Models\LoyaltyMission;
use Haida\FilamentLoyaltyClub\Models\LoyaltyMissionProgress;

class LoyaltyMissionService
{
    public function __construct(protected LoyaltyLedgerService $ledgerService) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public function handleEvent(LoyaltyEvent $event, array $payload): void
    {
        $customer = $event->customer;
        if (! $customer) {
            return;
        }

        $missions = LoyaltyMission::query()
            ->where('tenant_id', $customer->tenant_id)
            ->where('status', 'active')
            ->get();

        foreach ($missions as $mission) {
            $criteria = (array) ($mission->criteria ?? []);
            $expectedType = $criteria['event_type'] ?? null;
            if ($expectedType && $expectedType !== $event->type) {
                continue;
            }

            $target = (int) ($criteria['target'] ?? 1);
            $progress = LoyaltyMissionProgress::query()->firstOrCreate([
                'tenant_id' => $customer->tenant_id,
                'mission_id' => $mission->getKey(),
                'customer_id' => $customer->getKey(),
            ], [
                'progress' => 0,
                'target' => $target,
                'status' => 'in_progress',
            ]);

            if ($progress->status === 'completed') {
                continue;
            }

            $progress->progress += 1;
            $progress->target = max($progress->target, $target);

            if ($progress->progress >= $progress->target) {
                $this->completeMission($mission, $progress);
            } else {
                $progress->save();
            }
        }
    }

    protected function completeMission(LoyaltyMission $mission, LoyaltyMissionProgress $progress): void
    {
        $progress->status = 'completed';
        $progress->completed_at = now();
        $progress->save();

        $customer = $progress->customer;
        if (! $customer) {
            return;
        }

        if ($mission->reward_points > 0) {
            $this->ledgerService->creditPoints($customer, (int) $mission->reward_points, 'mission:'.$mission->getKey().':'.$customer->getKey(), ['mission_id' => $mission->getKey()]);
        }

        if ($mission->reward_cashback > 0) {
            $this->ledgerService->creditCashback($customer, (float) $mission->reward_cashback, 'mission:cashback:'.$mission->getKey().':'.$customer->getKey(), ['mission_id' => $mission->getKey()]);
        }

        if ($mission->badge_id) {
            LoyaltyBadgeAward::query()->firstOrCreate([
                'tenant_id' => $customer->tenant_id,
                'badge_id' => $mission->badge_id,
                'customer_id' => $customer->getKey(),
            ], [
                'source' => 'mission',
                'awarded_at' => now(),
            ]);
        }
    }
}
