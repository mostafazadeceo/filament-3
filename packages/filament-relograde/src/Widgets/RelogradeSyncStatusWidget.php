<?php

namespace Haida\FilamentRelograde\Widgets;

use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Haida\FilamentRelograde\Models\RelogradeAccount;
use Haida\FilamentRelograde\Models\RelogradeBrand;
use Haida\FilamentRelograde\Models\RelogradeOrder;
use Haida\FilamentRelograde\Models\RelogradeProduct;
use Haida\FilamentRelograde\Widgets\Concerns\RelogradeWidgetVisibility;

class RelogradeSyncStatusWidget extends StatsOverviewWidget
{
    use RelogradeWidgetVisibility;

    protected ?string $heading = 'وضعیت همگام‌سازی';

    protected function getStats(): array
    {
        $brandCount = RelogradeBrand::query()->count();
        $productCount = RelogradeProduct::query()->count();
        $accountCount = RelogradeAccount::query()->count();
        $orderCount = RelogradeOrder::query()->count();

        $brandSync = RelogradeBrand::query()->max('synced_at');
        $productSync = RelogradeProduct::query()->max('synced_at');
        $accountSync = RelogradeAccount::query()->max('synced_at');
        $orderSync = RelogradeOrder::query()->max('last_synced_at');

        return [
            Stat::make('برندها', (string) $brandCount)
                ->description('آخرین همگام‌سازی: '.$this->formatSyncTimestamp($brandSync)),
            Stat::make('محصولات', (string) $productCount)
                ->description('آخرین همگام‌سازی: '.$this->formatSyncTimestamp($productSync)),
            Stat::make('موجودی‌ها', (string) $accountCount)
                ->description('آخرین همگام‌سازی: '.$this->formatSyncTimestamp($accountSync)),
            Stat::make('سفارش‌ها', (string) $orderCount)
                ->description('آخرین همگام‌سازی: '.$this->formatSyncTimestamp($orderSync)),
        ];
    }

    private function formatSyncTimestamp(mixed $value): string
    {
        if (blank($value)) {
            return '-';
        }

        return Carbon::parse($value)->locale('fa')->diffForHumans();
    }
}
