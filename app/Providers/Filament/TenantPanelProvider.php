<?php

namespace App\Providers\Filament;

use Filamat\IamSuite\FilamatIamSuitePlugin;
use Filamat\IamSuite\Models\Tenant;
use Haida\FilamentAiCore\FilamentAiCorePlugin;
use Haida\FilamentRestaurantOps\FilamentRestaurantOpsPlugin;
use Haida\FilamentPettyCashIr\FilamentPettyCashIrPlugin;
use Haida\FilamentMeetings\FilamentMeetingsPlugin;
use Haida\FilamentWorkhub\FilamentWorkhubPlugin;
use Haida\FilamentLoyaltyClub\FilamentLoyaltyClubPlugin;
use Haida\FilamentChat\FilamentChatPlugin;
use Haida\ContentCms\ContentCmsPlugin;
use Haida\Blog\BlogPlugin;
use Haida\CommerceCatalog\CommerceCatalogPlugin;
use Haida\CommerceOrders\CommerceOrdersPlugin;
use Haida\FilamentCommerceCore\FilamentCommerceCorePlugin;
use Haida\FilamentCommerceExperience\FilamentCommerceExperiencePlugin;
use Haida\FilamentCryptoCore\FilamentCryptoCorePlugin;
use Haida\FilamentCryptoGateway\FilamentCryptoGatewayPlugin;
use Haida\FilamentCryptoNodes\FilamentCryptoNodesPlugin;
use Haida\FilamentMarketplaceConnectors\FilamentMarketplaceConnectorsPlugin;
use Haida\FilamentPayments\FilamentPaymentsPlugin;
use Haida\FilamentPos\FilamentPosPlugin;
use Haida\FilamentStorefrontBuilder\FilamentStorefrontBuilderPlugin;
use Haida\ProvidersCore\ProvidersCorePlugin;
use Haida\FilamentProvidersEsimGo\ProvidersEsimGoPlugin;
use Haida\FilamentMailtrap\MailtrapPlugin;
use Haida\FilamentMailOps\MailOpsPlugin;
use Haida\SmsBulk\FilamentSmsBulkPlugin;
use Haida\FilamentThreeCx\Filament\FilamentThreeCxPlugin;
use Haida\PageBuilder\PageBuilderPlugin;
use Haida\SiteBuilderCore\SiteBuilderCorePlugin;
use Haida\TenancyDomains\TenancyDomainsPlugin;
use Vendor\FilamentAccountingIr\FilamentAccountingIrPlugin;
use Vendor\FilamentPayrollAttendanceIr\FilamentPayrollAttendanceIrPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\FontProviders\LocalFontProvider;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Navigation\NavigationBuilder;
use Filament\Enums\ThemeMode;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use ZPMLabs\FilamentApiDocsBuilder\FilamentApiDocsBuilderPlugin;
use App\Support\Navigation\AppNavigationBuilder as AbrakAppNavigationBuilder;
use App\Http\Controllers\Filament\AppSwitchController;
use Illuminate\Support\Facades\Route;

class TenantPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('tenant')
            ->path('tenant')
            ->login()
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->defaultThemeMode(ThemeMode::Dark)
            ->brandName('Abrak')
            ->brandLogo(asset('brand/abrak-mark.svg'))
            ->darkModeBrandLogo(asset('brand/abrak-mark.svg'))
            ->favicon(asset('brand/abrak-mark.svg'))
            // No CDN: serve the UI font from this server only.
            ->font('Vazirmatn', asset('fonts/vazirmatn/vazirmatn.css'), LocalFontProvider::class)
            // Most tenant-scoped models use a `tenant()` relationship.
            // UserResource overrides this to `tenants()` for the many-to-many user linkage.
            ->tenant(Tenant::class, 'slug', 'tenant')
            ->authenticatedTenantRoutes(function (): void {
                Route::get('/app/{key?}', AppSwitchController::class)->name('app.switch');
            })
            ->colors([
                'primary' => Color::Cyan,
            ])
            ->navigation(fn (): NavigationBuilder => app(NavigationBuilder::class)->groups(
                AbrakAppNavigationBuilder::build()
            ))
            ->navigationGroups([
                'مدیریت سازمان',
                'مدیریت دسترسی',
                'اشتراک',
                'کیف پول',
                'اعلان‌ها',
                'اتوماسیون',
                'گزارش‌ها',
                'تنظیمات',
                'راهنما',
            ])
            ->widgets([])
            ->plugins([
                FilamentAiCorePlugin::make(),
                FilamentWorkhubPlugin::make(),
                FilamentMeetingsPlugin::make(),
                ...(class_exists(FilamentChatPlugin::class) ? [FilamentChatPlugin::make()] : []),
                ...(class_exists(FilamentLoyaltyClubPlugin::class) ? [FilamentLoyaltyClubPlugin::make()] : []),
                SiteBuilderCorePlugin::make(),
                TenancyDomainsPlugin::make(),
                PageBuilderPlugin::make(),
                ContentCmsPlugin::make(),
                BlogPlugin::make(),
                CommerceCatalogPlugin::make(),
                CommerceOrdersPlugin::make(),
                ...(class_exists(FilamentCommerceCorePlugin::class) ? [FilamentCommerceCorePlugin::make()] : []),
                ...(class_exists(FilamentStorefrontBuilderPlugin::class) ? [FilamentStorefrontBuilderPlugin::make()] : []),
                ...(class_exists(FilamentCommerceExperiencePlugin::class) ? [FilamentCommerceExperiencePlugin::make()] : []),
                ...(class_exists(FilamentCryptoCorePlugin::class) ? [FilamentCryptoCorePlugin::make()] : []),
                ...(class_exists(FilamentCryptoGatewayPlugin::class) ? [FilamentCryptoGatewayPlugin::make()] : []),
                ...(class_exists(FilamentCryptoNodesPlugin::class) ? [FilamentCryptoNodesPlugin::make()] : []),
                ...(class_exists(FilamentPaymentsPlugin::class) ? [FilamentPaymentsPlugin::make()] : []),
                ...(class_exists(FilamentPosPlugin::class) ? [FilamentPosPlugin::make()] : []),
                ...(class_exists(FilamentMarketplaceConnectorsPlugin::class) ? [FilamentMarketplaceConnectorsPlugin::make()] : []),
                ProvidersCorePlugin::make(),
                ProvidersEsimGoPlugin::make(),
                ...(class_exists(MailtrapPlugin::class) ? [MailtrapPlugin::make()] : []),
                ...(class_exists(MailOpsPlugin::class) ? [MailOpsPlugin::make()] : []),
                FilamentAccountingIrPlugin::make(),
                FilamentPayrollAttendanceIrPlugin::make(),
                FilamentRestaurantOpsPlugin::make(),
                ...(class_exists(FilamentSmsBulkPlugin::class) ? [FilamentSmsBulkPlugin::make()] : []),
                ...(class_exists(FilamentThreeCxPlugin::class) ? [FilamentThreeCxPlugin::make()] : []),
                FilamentPettyCashIrPlugin::make(),
                FilamatIamSuitePlugin::make()
                    ->superAdminPanels(['admin'])
                    ->tenantPanels(['tenant']),
                FilamentApiDocsBuilderPlugin::make(),
            ])
            ->renderHook(\Filament\View\PanelsRenderHook::SIDEBAR_NAV_START, fn () => view('filamat-iam::components.sidebar-role'))
            ->renderHook(\Filament\View\PanelsRenderHook::TOPBAR_END, fn () => view('filament.components.topbar-tools'))
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                \App\Http\Middleware\SetLocale::class,
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
