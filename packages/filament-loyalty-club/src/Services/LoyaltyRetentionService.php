<?php

namespace Haida\FilamentLoyaltyClub\Services;

use Haida\FilamentLoyaltyClub\Models\LoyaltyAuditEvent;
use Haida\FilamentLoyaltyClub\Models\LoyaltyCampaignDispatch;
use Haida\FilamentLoyaltyClub\Models\LoyaltyEvent;
use Haida\FilamentLoyaltyClub\Models\LoyaltyFraudSignal;

class LoyaltyRetentionService
{
    /**
     * @return array<string, int>
     */
    public function prune(): array
    {
        $auditDays = (int) config('filament-loyalty-club.retention.audit_days', 730);
        $eventsDays = (int) config('filament-loyalty-club.retention.events_days', 365);
        $fraudDays = (int) config('filament-loyalty-club.retention.fraud_days', 730);
        $campaignDays = (int) config('filament-loyalty-club.retention.campaign_days', 365);

        $auditCutoff = now()->subDays($auditDays);
        $eventCutoff = now()->subDays($eventsDays);
        $fraudCutoff = now()->subDays($fraudDays);
        $campaignCutoff = now()->subDays($campaignDays);

        $audits = LoyaltyAuditEvent::query()
            ->where(function ($query) use ($auditCutoff) {
                $query->whereNotNull('occurred_at')->where('occurred_at', '<', $auditCutoff)
                    ->orWhereNull('occurred_at')->where('created_at', '<', $auditCutoff);
            })
            ->delete();

        $events = LoyaltyEvent::query()
            ->where(function ($query) use ($eventCutoff) {
                $query->whereNotNull('occurred_at')->where('occurred_at', '<', $eventCutoff)
                    ->orWhereNull('occurred_at')->where('created_at', '<', $eventCutoff);
            })
            ->delete();

        $frauds = LoyaltyFraudSignal::query()
            ->where(function ($query) use ($fraudCutoff) {
                $query->whereNotNull('detected_at')->where('detected_at', '<', $fraudCutoff)
                    ->orWhereNull('detected_at')->where('created_at', '<', $fraudCutoff);
            })
            ->delete();

        $dispatches = LoyaltyCampaignDispatch::query()
            ->where('created_at', '<', $campaignCutoff)
            ->delete();

        return [
            'audits' => $audits,
            'events' => $events,
            'frauds' => $frauds,
            'campaign_dispatches' => $dispatches,
        ];
    }
}
