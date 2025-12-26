<?php

namespace Haida\FilamentRelograde\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Haida\FilamentRelograde\Models\RelogradeConnection;
use Haida\FilamentRelograde\Support\RelogradeLabels;
use Haida\FilamentRelograde\Widgets\Concerns\RelogradeWidgetVisibility;

class RelogradeBalanceWidget extends StatsOverviewWidget
{
    use RelogradeWidgetVisibility;

    protected ?string $heading = 'موجودی‌ها';

    protected function getStats(): array
    {
        $connection = RelogradeConnection::query()->default()->first();
        if (! $connection) {
            return [Stat::make('اتصالی وجود ندارد', '-')];
        }

        $accounts = $connection->accounts()->get();
        if ($accounts->isEmpty()) {
            return [Stat::make('موجودی‌ای یافت نشد', '-')];
        }

        return $accounts->map(function ($account) {
            return Stat::make($account->currency, (string) $account->total_amount)
                ->description(RelogradeLabels::environment($account->state));
        })->all();
    }
}
