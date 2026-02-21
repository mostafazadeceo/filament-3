<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Haida\SmsBulk\Models\SmsBulkCampaignRecipient;

class SmsBulkQueueHealthWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make(__('filament-sms-bulk::messages.widgets.queue_queued'), (string) SmsBulkCampaignRecipient::query()->where('status', 'queued')->count()),
            Stat::make(__('filament-sms-bulk::messages.widgets.queue_sent'), (string) SmsBulkCampaignRecipient::query()->where('status', 'sent')->count()),
            Stat::make(__('filament-sms-bulk::messages.widgets.queue_suppressed'), (string) SmsBulkCampaignRecipient::query()->where('status', 'suppressed')->count()),
            Stat::make(__('filament-sms-bulk::messages.widgets.queue_failed'), (string) SmsBulkCampaignRecipient::query()->where('status', 'failed')->count()),
        ];
    }
}
