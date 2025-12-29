<?php

namespace App\Providers\Filament;

use Filamat\IamSuite\FilamatIamSuitePlugin;
use Filamat\IamSuite\Models\Tenant;
use Haida\FilamentRestaurantOps\FilamentRestaurantOpsPlugin;
use Haida\FilamentPettyCashIr\FilamentPettyCashIrPlugin;
use Haida\FilamentWorkhub\FilamentWorkhubPlugin;
use Vendor\FilamentAccountingIr\FilamentAccountingIrPlugin;
use Vendor\FilamentPayrollAttendanceIr\FilamentPayrollAttendanceIrPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use ZPMLabs\FilamentApiDocsBuilder\FilamentApiDocsBuilderPlugin;

class TenantPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('tenant')
            ->path('tenant')
            ->login()
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->tenant(Tenant::class, 'slug', 'users')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->widgets([
                AccountWidget::class,
            ])
            ->plugins([
                FilamentWorkhubPlugin::make(),
                FilamentAccountingIrPlugin::make(),
                FilamentPayrollAttendanceIrPlugin::make(),
                FilamentRestaurantOpsPlugin::make(),
                FilamentPettyCashIrPlugin::make(),
                FilamatIamSuitePlugin::make()
                    ->superAdminPanels(['admin'])
                    ->tenantPanels(['tenant']),
                FilamentApiDocsBuilderPlugin::make(),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
