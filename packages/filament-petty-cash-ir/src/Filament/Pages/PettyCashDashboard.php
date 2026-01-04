<?php

namespace Haida\FilamentPettyCashIr\Filament\Pages;

use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Pages\Dashboard;
use Haida\FilamentPettyCashIr\Filament\Widgets\PettyCashOverviewWidget;

class PettyCashDashboard extends Dashboard
{
    protected static string $routePath = 'petty-cash/dashboard';

    protected static ?string $navigationLabel = 'داشبورد';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static string|\UnitEnum|null $navigationGroup = 'تنخواه';

    protected static ?int $navigationSort = 0;

    public static function canView(): bool
    {
        return IamAuthorization::allows('petty_cash.report.view')
            || IamAuthorization::allows('petty_cash.exceptions.view');
    }

    public function getWidgets(): array
    {
        return [
            PettyCashOverviewWidget::class,
        ];
    }
}
