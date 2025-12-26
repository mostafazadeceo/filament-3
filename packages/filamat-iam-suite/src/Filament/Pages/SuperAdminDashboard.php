<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Pages;

use Filamat\IamSuite\Filament\Concerns\AuthorizesIam;
use Filamat\IamSuite\Filament\Widgets\NotificationDeliveryChartWidget;
use Filamat\IamSuite\Filament\Widgets\NotificationStatsWidget;
use Filamat\IamSuite\Filament\Widgets\QuickActionsWidget;
use Filamat\IamSuite\Filament\Widgets\RecentAuditLogsWidget;
use Filamat\IamSuite\Filament\Widgets\RecentSecurityEventsWidget;
use Filamat\IamSuite\Filament\Widgets\SuperAdminStatsWidget;
use Filamat\IamSuite\Filament\Widgets\WalletVolumeChartWidget;
use Filamat\IamSuite\Filament\Widgets\WebhookHealthChartWidget;
use Filament\Pages\Dashboard;

class SuperAdminDashboard extends Dashboard
{
    use AuthorizesIam;

    protected static ?string $permission = 'iam.view';

    protected static ?string $slug = 'dashboard';

    protected static ?string $navigationLabel = 'داشبورد کلان';

    protected static ?string $title = 'داشبورد کلان';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static string|\UnitEnum|null $navigationGroup = 'گزارش‌ها';

    protected function getHeaderWidgets(): array
    {
        return [
            SuperAdminStatsWidget::class,
            NotificationStatsWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            QuickActionsWidget::class,
            WalletVolumeChartWidget::class,
            NotificationDeliveryChartWidget::class,
            WebhookHealthChartWidget::class,
            RecentSecurityEventsWidget::class,
            RecentAuditLogsWidget::class,
        ];
    }
}
