<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Pages;

use Filamat\IamSuite\Filament\Concerns\AuthorizesIam;
use Filamat\IamSuite\Filament\Widgets\MegaAdminDeliveryWidget;
use Filamat\IamSuite\Filament\Widgets\MegaAdminOperationsWidget;
use Filamat\IamSuite\Filament\Widgets\MegaAdminOverviewWidget;
use Filamat\IamSuite\Filament\Widgets\NotificationDeliveryChartWidget;
use Filamat\IamSuite\Filament\Widgets\QuickActionsWidget;
use Filamat\IamSuite\Filament\Widgets\RecentAuditLogsWidget;
use Filamat\IamSuite\Filament\Widgets\RecentNotificationsWidget;
use Filamat\IamSuite\Filament\Widgets\RecentSecurityEventsWidget;
use Filamat\IamSuite\Filament\Widgets\WalletVolumeChartWidget;
use Filament\Pages\Dashboard;

class SuperAdminDashboard extends Dashboard
{
    use AuthorizesIam;

    protected static ?string $permission = 'iam.view';

    protected static ?string $slug = 'dashboard';

    protected static ?string $navigationLabel = 'داشبورد کلان';

    protected static ?string $title = 'داشبورد کلان';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static string|\UnitEnum|null $navigationGroup = 'مدیریت کلان';

    protected static ?int $navigationSort = 0;

    public function getWidgets(): array
    {
        return [
            QuickActionsWidget::class,
            MegaAdminOverviewWidget::class,
            MegaAdminOperationsWidget::class,
            MegaAdminDeliveryWidget::class,
            WalletVolumeChartWidget::class,
            NotificationDeliveryChartWidget::class,
            RecentSecurityEventsWidget::class,
            RecentNotificationsWidget::class,
            RecentAuditLogsWidget::class,
        ];
    }
}
