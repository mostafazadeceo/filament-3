<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Pages;

use App\Filament\Widgets\AppLauncherWidget;
use App\Support\Navigation\AppContext;
use Filamat\IamSuite\Filament\Concerns\AuthorizesIam;
use Filament\Pages\Dashboard;

class TenantDashboard extends Dashboard
{
    use AuthorizesIam;

    // Dashboard should always be reachable for authenticated tenant members.
    // Access to each module is enforced by navigation/resource permissions.
    protected static ?string $permission = null;

    protected static ?string $navigationLabel = 'داشبورد';

    protected static ?string $title = 'داشبورد فضای کاری';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';

    protected static string|\UnitEnum|null $navigationGroup = 'گزارش‌ها';

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
