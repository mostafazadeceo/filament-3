<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Pages;

use App\Filament\Widgets\AppLauncherWidget;
use App\Support\Navigation\AppContext;
use Filamat\IamSuite\Filament\Concerns\AuthorizesIam;
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

    public function mount(): void
    {
        // Landing here should always show the app launcher (Odoo-style).
        AppContext::set(null);
    }

    public function getWidgets(): array
    {
        return [
            AppLauncherWidget::class,
        ];
    }
}
