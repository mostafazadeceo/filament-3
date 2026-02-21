<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Haida\SmsBulk\Models\SmsBulkCampaign;
use Haida\SmsBulk\Models\SmsBulkCampaignRecipient;
use Haida\SmsBulk\Models\SmsBulkProviderConnection;

class SmsBulkOverviewWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $credit = (float) (SmsBulkProviderConnection::query()->max('last_credit_snapshot') ?? 0);

        return [
            Stat::make(__('filament-sms-bulk::messages.widgets.credit'), (string) $credit),
            Stat::make(__('filament-sms-bulk::messages.widgets.active_campaigns'), (string) SmsBulkCampaign::query()->whereIn('status', ['queued', 'sending'])->count()),
            Stat::make(__('filament-sms-bulk::messages.widgets.pending_approvals'), (string) SmsBulkCampaign::query()->where('approval_state', 'pending')->count()),
            Stat::make(__('filament-sms-bulk::messages.widgets.failed_recipients'), (string) SmsBulkCampaignRecipient::query()->where('status', 'failed')->count()),
        ];
    }
}
