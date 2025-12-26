<?php

namespace Haida\FilamentRelograde\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Haida\FilamentRelograde\Models\RelogradeAlert;
use Haida\FilamentRelograde\Widgets\Concerns\RelogradeWidgetVisibility;

class RelogradeAlertsWidget extends StatsOverviewWidget
{
    use RelogradeWidgetVisibility;

    protected ?string $heading = 'هشدارهای فعال';

    protected function getStats(): array
    {
        $active = RelogradeAlert::query()->whereNull('resolved_at')->count();
        $critical = RelogradeAlert::query()->whereNull('resolved_at')->where('severity', 'critical')->count();
        $warning = RelogradeAlert::query()->whereNull('resolved_at')->where('severity', 'warning')->count();

        return [
            Stat::make('فعال', (string) $active),
            Stat::make('بحرانی', (string) $critical)->color('danger'),
            Stat::make('هشدار', (string) $warning)->color('warning'),
        ];
    }
}
