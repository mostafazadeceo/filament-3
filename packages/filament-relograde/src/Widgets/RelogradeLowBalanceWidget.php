<?php

namespace Haida\FilamentRelograde\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Haida\FilamentRelograde\Models\RelogradeConnection;
use Haida\FilamentRelograde\Widgets\Concerns\RelogradeWidgetVisibility;

class RelogradeLowBalanceWidget extends StatsOverviewWidget
{
    use RelogradeWidgetVisibility;

    protected ?string $heading = 'هشدارهای موجودی کم';

    protected function getStats(): array
    {
        $thresholds = config('relograde.low_balance_thresholds', []);
        if (! is_array($thresholds) || $thresholds === []) {
            return [Stat::make('آستانه‌ها تنظیم نشده‌اند', '-')];
        }

        $connection = RelogradeConnection::query()->default()->first();
        if (! $connection) {
            return [Stat::make('اتصالی وجود ندارد', '-')];
        }

        $accounts = $connection->accounts()->get();
        $stats = [];

        foreach ($accounts as $account) {
            $threshold = $thresholds[$account->currency] ?? null;
            if ($threshold === null) {
                continue;
            }

            if ($account->total_amount < $threshold) {
                $stats[] = Stat::make($account->currency, (string) $account->total_amount)
                    ->description('کمتر از آستانه: '.$threshold)
                    ->color('danger');
            }
        }

        return $stats ?: [Stat::make('همه موجودی‌ها سالم هستند', '-')];
    }
}
