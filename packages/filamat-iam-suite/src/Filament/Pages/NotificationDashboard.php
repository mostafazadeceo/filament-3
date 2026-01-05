<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Pages;

use Filamat\IamSuite\Filament\Concerns\AuthorizesIam;
use Filamat\IamSuite\Filament\Widgets\NotificationChannelBreakdownWidget;
use Filamat\IamSuite\Filament\Widgets\NotificationFailuresWidget;
use Filamat\IamSuite\Filament\Widgets\NotificationStatsWidget;
use Filamat\IamSuite\Filament\Widgets\NotificationStatusTimelineWidget;
use Filamat\IamSuite\Filament\Widgets\WebhookHealthChartWidget;
use Filament\Pages\Dashboard;

class NotificationDashboard extends Dashboard
{
    use AuthorizesIam;

    protected static ?string $permission = 'notification.view';

    protected static string $routePath = '/notifications-dashboard';

    protected static ?string $navigationLabel = 'داشبورد اعلان‌ها';

    protected static ?string $title = 'داشبورد اعلان‌ها';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bell-alert';

    protected static string|\UnitEnum|null $navigationGroup = 'اعلان‌ها';

    protected function getHeaderWidgets(): array
    {
        return [
            NotificationStatsWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            NotificationStatusTimelineWidget::class,
            NotificationChannelBreakdownWidget::class,
            WebhookHealthChartWidget::class,
            NotificationFailuresWidget::class,
        ];
    }
}
