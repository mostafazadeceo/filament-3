<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Pages;

use Filamat\IamSuite\Filament\Concerns\AuthorizesIam;
use Filamat\IamSuite\Filament\Widgets\NotificationDeliveryChartWidget;
use Filamat\IamSuite\Filament\Widgets\NotificationStatsWidget;
use Filamat\IamSuite\Filament\Widgets\QuickActionsWidget;
use Filamat\IamSuite\Filament\Widgets\RecentAuditLogsWidget;
use Filamat\IamSuite\Filament\Widgets\RecentSecurityEventsWidget;
use Filamat\IamSuite\Filament\Widgets\TenantStatsWidget;
use Filamat\IamSuite\Filament\Widgets\WalletVolumeChartWidget;
use Filament\Pages\Dashboard;

class TenantDashboard extends Dashboard
{
    use AuthorizesIam;

    protected static ?string $permission = 'iam.view';

    protected static ?string $slug = 'dashboard';

    protected static ?string $navigationLabel = 'داشبورد';

    protected static ?string $title = 'داشبورد فضای کاری';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';

    protected static string|\UnitEnum|null $navigationGroup = 'گزارش‌ها';

    protected function getHeaderWidgets(): array
    {
        return [
            TenantStatsWidget::class,
            NotificationStatsWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            QuickActionsWidget::class,
            WalletVolumeChartWidget::class,
            NotificationDeliveryChartWidget::class,
            RecentSecurityEventsWidget::class,
            RecentAuditLogsWidget::class,
        ];
    }
}
