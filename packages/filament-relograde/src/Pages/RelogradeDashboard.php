<?php

namespace Haida\FilamentRelograde\Pages;

use Filament\Pages\Dashboard;
use Haida\FilamentRelograde\Widgets\RelogradeAlertsWidget;
use Haida\FilamentRelograde\Widgets\RelogradeBalanceWidget;
use Haida\FilamentRelograde\Widgets\RelogradeLowBalanceWidget;
use Haida\FilamentRelograde\Widgets\RelogradeOrdersStatusWidget;
use Haida\FilamentRelograde\Widgets\RelogradeStockWidget;
use Haida\FilamentRelograde\Widgets\RelogradeSyncStatusWidget;

class RelogradeDashboard extends Dashboard
{
    protected static string $routePath = 'relograde/dashboard';

    protected static ?string $navigationLabel = 'داشبورد';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static string|\UnitEnum|null $navigationGroup = 'رلوگرید';

    protected static ?int $navigationSort = 0;

    public function getWidgets(): array
    {
        return [
            RelogradeBalanceWidget::class,
            RelogradeSyncStatusWidget::class,
            RelogradeOrdersStatusWidget::class,
            RelogradeLowBalanceWidget::class,
            RelogradeStockWidget::class,
            RelogradeAlertsWidget::class,
        ];
    }
}
