<?php

namespace Haida\FilamentLoyaltyClub\Services;

use Haida\FilamentLoyaltyClub\Models\LoyaltyPointsBucket;
use Haida\FilamentLoyaltyClub\Models\LoyaltyWalletAccount;
use Haida\FilamentLoyaltyClub\Models\LoyaltyWalletLedger;
use Haida\FilamentNotify\Core\Support\Triggers\TriggerDispatcher;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Collection;

class LoyaltyExpiryService
{
    public function __construct(
        protected DatabaseManager $db,
        protected LoyaltyAuditService $auditService,
    ) {}

    public function expirePoints(): int
    {
        $expiredCount = 0;
        $buckets = LoyaltyPointsBucket::query()
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->where('points_available', '>', 0)
            ->orderBy('expires_at')
            ->get();

        foreach ($buckets as $bucket) {
            $expiredCount += $this->expireBucket($bucket);
        }

        return $expiredCount;
    }

    public function notifyUpcomingExpiries(): int
    {
        $days = array_filter((array) config('filament-loyalty-club.points.expiry.notify_days_before', [30, 7, 1]));
        if ($days === []) {
            return 0;
        }

        $panelId = (string) config('filament-loyalty-club.campaigns.panel', 'tenant');
        $eventName = 'loyalty_points_expiring';
        $sent = 0;

        foreach ($days as $day) {
            $targetDate = now()->addDays((int) $day)->toDateString();
            $buckets = LoyaltyPointsBucket::query()
                ->whereDate('expires_at', $targetDate)
                ->where('points_available', '>', 0)
                ->get()
                ->groupBy('customer_id');

            $sent += $this->dispatchExpiryNotifications($buckets, $panelId, $eventName, (int) $day);
        }

        return $sent;
    }

    protected function expireBucket(LoyaltyPointsBucket $bucket): int
    {
        return $this->db->transaction(function () use ($bucket) {
            $bucket->refresh();
            if ($bucket->points_available <= 0) {
                return 0;
            }

            $account = LoyaltyWalletAccount::query()
                ->where('tenant_id', $bucket->tenant_id)
                ->where('customer_id', $bucket->customer_id)
                ->lockForUpdate()
                ->first();

            if (! $account) {
                return 0;
            }

            $expired = (int) $bucket->points_available;
            $account->points_balance = max(0, $account->points_balance - $expired);
            $account->save();

            $bucket->points_available = 0;
            $bucket->save();

            LoyaltyWalletLedger::query()->create([
                'tenant_id' => $bucket->tenant_id,
                'customer_id' => $bucket->customer_id,
                'event_id' => null,
                'type' => 'expiry',
                'points_delta' => -$expired,
                'cashback_delta' => 0,
                'balance_after_points' => $account->points_balance,
                'balance_after_cashback' => $account->cashback_balance,
                'status' => 'posted',
                'idempotency_key' => 'expiry:bucket:'.$bucket->getKey(),
                'reference_type' => LoyaltyPointsBucket::class,
                'reference_id' => $bucket->getKey(),
                'meta' => ['expires_at' => $bucket->expires_at],
            ]);

            $this->auditService->record('points_expired', [
                'bucket_id' => $bucket->getKey(),
                'points' => $expired,
            ], $bucket);

            return $expired;
        });
    }

    protected function dispatchExpiryNotifications(Collection $buckets, string $panelId, string $eventName, int $days): int
    {
        if ($panelId === '' || ! class_exists(TriggerDispatcher::class)) {
            return 0;
        }

        $count = 0;
        foreach ($buckets as $customerId => $rows) {
            $points = $rows->sum('points_available');
            if ($points <= 0) {
                continue;
            }
            try {
                $customer = \Haida\FilamentLoyaltyClub\Models\LoyaltyCustomer::query()->find($customerId);
                if (! $customer) {
                    continue;
                }

                $customer->setAttribute('expiring_points', $points);
                $customer->setAttribute('expiring_days', $days);
                app(TriggerDispatcher::class)->dispatchForEloquent($panelId, $customer, $eventName);
                $count += 1;
            } catch (\Throwable) {
                // Ignore notification failures.
            }
        }

        return $count;
    }
}
