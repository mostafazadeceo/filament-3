<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Services;

use Haida\SmsBulk\Exceptions\QuotaExceededException;
use Haida\SmsBulk\Models\SmsBulkCampaignRecipient;
use Haida\SmsBulk\Models\SmsBulkQuotaPolicy;
use Illuminate\Support\Carbon;

class QuotaService
{
    /**
     * @throws QuotaExceededException
     */
    public function assertCanEnqueue(int $tenantId, int $recipientCount, float $estimatedCost): void
    {
        $policy = SmsBulkQuotaPolicy::query()->where('tenant_id', $tenantId)->first();
        if (! $policy) {
            return;
        }

        $todayStart = Carbon::now()->startOfDay();
        $monthStart = Carbon::now()->startOfMonth();

        $dailyRecipients = SmsBulkCampaignRecipient::query()
            ->where('tenant_id', $tenantId)
            ->where('created_at', '>=', $todayStart)
            ->count();

        $monthlyRecipients = SmsBulkCampaignRecipient::query()
            ->where('tenant_id', $tenantId)
            ->where('created_at', '>=', $monthStart)
            ->count();

        $dailySpend = (float) SmsBulkCampaignRecipient::query()
            ->where('tenant_id', $tenantId)
            ->where('created_at', '>=', $todayStart)
            ->sum('cost');

        $monthlySpend = (float) SmsBulkCampaignRecipient::query()
            ->where('tenant_id', $tenantId)
            ->where('created_at', '>=', $monthStart)
            ->sum('cost');

        if ($policy->max_daily_recipients !== null && ($dailyRecipients + $recipientCount) > $policy->max_daily_recipients) {
            throw new QuotaExceededException('Daily recipient quota exceeded.');
        }

        if ($policy->max_monthly_recipients !== null && ($monthlyRecipients + $recipientCount) > $policy->max_monthly_recipients) {
            throw new QuotaExceededException('Monthly recipient quota exceeded.');
        }

        if ($policy->max_daily_spend !== null && ($dailySpend + $estimatedCost) > (float) $policy->max_daily_spend) {
            throw new QuotaExceededException('Daily spend quota exceeded.');
        }

        if ($policy->max_monthly_spend !== null && ($monthlySpend + $estimatedCost) > (float) $policy->max_monthly_spend) {
            throw new QuotaExceededException('Monthly spend quota exceeded.');
        }
    }

    public function requiresApproval(int $tenantId, float $estimatedCost): bool
    {
        $policy = SmsBulkQuotaPolicy::query()->where('tenant_id', $tenantId)->first();
        if (! $policy || $policy->requires_approval_over_amount === null) {
            return false;
        }

        return $estimatedCost > (float) $policy->requires_approval_over_amount;
    }
}
