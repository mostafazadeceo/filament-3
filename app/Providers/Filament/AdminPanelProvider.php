<?php

namespace App\Providers\Filament;

use App\Settings\GeneralSettings;
use App\Support\Calendar\CalendarFormatter;
use DiogoGPinto\AuthUIEnhancer\AuthUIEnhancerPlugin;
use Filamat\IamSuite\FilamatIamSuitePlugin;
use Haida\FilamentAiCore\FilamentAiCorePlugin;
use Haida\FilamentNotify\Core\FilamentNotifyPlugin;
use Haida\FilamentCurrencyRates\CurrencyRatesPlugin;
use Haida\FilamentPettyCashIr\FilamentPettyCashIrPlugin;
use Haida\FilamentRelograde\RelogradePlugin;
use Haida\FilamentProvidersEsimGo\ProvidersEsimGoPlugin;
use Haida\FilamentMailtrap\MailtrapPlugin;
use Haida\FilamentMailOps\MailOpsPlugin;
use Haida\FilamentRestaurantOps\FilamentRestaurantOpsPlugin;
use Haida\FilamentThreeCx\Filament\FilamentThreeCxPlugin;
use Haida\FilamentWorkhub\FilamentWorkhubPlugin;
use Haida\FilamentLoyaltyClub\FilamentLoyaltyClubPlugin;
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
use Haida\PlatformCore\PlatformCorePlugin;
use Haida\PageBuilder\PageBuilderPlugin;
use Haida\SiteBuilderCore\SiteBuilderCorePlugin;
use Haida\TenancyDomains\TenancyDomainsPlugin;
use Vendor\FilamentAccountingIr\FilamentAccountingIrPlugin;
use Vendor\FilamentPayrollAttendanceIr\FilamentPayrollAttendanceIrPlugin;
use Filament\FontProviders\LocalFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use App\Filament\Widgets\PanelInfoWidget;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use ZPMLabs\FilamentApiDocsBuilder\FilamentApiDocsBuilderPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $plugins = [];

        $resolveSettings = static function (): ?GeneralSettings {
            try {
                if (Schema::hasTable('settings')) {
                    return app(GeneralSettings::class);
                }
            } catch (\Throwable) {
                return null;
            }

            return null;
        };

        $settings = $resolveSettings();

        if ($settings?->enable_auth_ui_enhancer) {
            $authUiEnhancer = AuthUIEnhancerPlugin::make();

            $formPanelPosition = $settings->auth_ui_form_panel_position ?? 'right';
            if (in_array($formPanelPosition, ['left', 'right'], true)) {
                $authUiEnhancer->formPanelPosition($formPanelPosition);
            }

            $mobileFormPanelPosition = $settings->auth_ui_mobile_form_panel_position ?? 'top';
            if (in_array($mobileFormPanelPosition, ['top', 'bottom'], true)) {
                $authUiEnhancer->mobileFormPanelPosition($mobileFormPanelPosition);
            }

            $formPanelWidth = $settings->auth_ui_form_panel_width ?? '50%';
            if (is_string($formPanelWidth) && preg_match('/^\\d+(\\.\\d+)?(rem|%|px|em|vw|vh|pt)$/', $formPanelWidth)) {
                $authUiEnhancer->formPanelWidth($formPanelWidth);
            }

            $opacityValue = (int) ($settings->auth_ui_empty_panel_background_image_opacity ?? 100);
            $opacityValue = min(max($opacityValue, 0), 100);
            $authUiEnhancer->emptyPanelBackgroundImageOpacity($opacityValue . '%');

            $showEmptyPanelOnMobile = (bool) ($settings->auth_ui_show_empty_panel_on_mobile ?? true);
            $authUiEnhancer->showEmptyPanelOnMobile($showEmptyPanelOnMobile);

            $imagePath = $settings->auth_ui_empty_panel_background_image_path ?? null;
            if (filled($imagePath)) {
                $imageUrl = (str_starts_with($imagePath, 'http://')
                    || str_starts_with($imagePath, 'https://')
                    || str_starts_with($imagePath, '//'))
                    ? $imagePath
                    : asset('storage/' . ltrim($imagePath, '/'));
                $authUiEnhancer->emptyPanelBackgroundImageUrl($imageUrl);
            }

            $plugins[] = $authUiEnhancer;
        }

        $plugins[] = FilamentNotifyPlugin::make();
        $plugins[] = FilamentAiCorePlugin::make();
        $plugins[] = CurrencyRatesPlugin::make();
        $plugins[] = RelogradePlugin::make();
        $plugins[] = FilamentWorkhubPlugin::make();
        if (class_exists(FilamentLoyaltyClubPlugin::class)) {
            $plugins[] = FilamentLoyaltyClubPlugin::make();
        }
        $plugins[] = PlatformCorePlugin::make();
        $plugins[] = SiteBuilderCorePlugin::make();
        $plugins[] = TenancyDomainsPlugin::make();
        $plugins[] = PageBuilderPlugin::make();
        $plugins[] = ContentCmsPlugin::make();
        $plugins[] = BlogPlugin::make();
        $plugins[] = CommerceCatalogPlugin::make();
        $plugins[] = CommerceOrdersPlugin::make();
        if (class_exists(FilamentCommerceCorePlugin::class)) {
            $plugins[] = FilamentCommerceCorePlugin::make();
        }
        if (class_exists(FilamentStorefrontBuilderPlugin::class)) {
            $plugins[] = FilamentStorefrontBuilderPlugin::make();
        }
        if (class_exists(FilamentCommerceExperiencePlugin::class)) {
            $plugins[] = FilamentCommerceExperiencePlugin::make();
        }
        if (class_exists(FilamentCryptoCorePlugin::class)) {
            $plugins[] = FilamentCryptoCorePlugin::make();
        }
        if (class_exists(FilamentCryptoGatewayPlugin::class)) {
            $plugins[] = FilamentCryptoGatewayPlugin::make();
        }
        if (class_exists(FilamentCryptoNodesPlugin::class)) {
            $plugins[] = FilamentCryptoNodesPlugin::make();
        }
        if (class_exists(FilamentPaymentsPlugin::class)) {
            $plugins[] = FilamentPaymentsPlugin::make();
        }
        if (class_exists(FilamentPosPlugin::class)) {
            $plugins[] = FilamentPosPlugin::make();
        }
        if (class_exists(FilamentMarketplaceConnectorsPlugin::class)) {
            $plugins[] = FilamentMarketplaceConnectorsPlugin::make();
        }
        $plugins[] = ProvidersCorePlugin::make();
        $plugins[] = ProvidersEsimGoPlugin::make();
        if (class_exists(MailtrapPlugin::class)) {
            $plugins[] = MailtrapPlugin::make();
        }
        if (class_exists(MailOpsPlugin::class)) {
            $plugins[] = MailOpsPlugin::make();
        }
        $plugins[] = FilamentAccountingIrPlugin::make();
        $plugins[] = FilamentPayrollAttendanceIrPlugin::make();
        $plugins[] = FilamentRestaurantOpsPlugin::make();
        if (class_exists(FilamentThreeCxPlugin::class)) {
            $plugins[] = FilamentThreeCxPlugin::make();
        }
        $plugins[] = FilamentPettyCashIrPlugin::make();
        $plugins[] = FilamatIamSuitePlugin::make()
            ->superAdminPanels(['admin'])
            ->tenantPanels(['tenant']);
        $plugins[] = FilamentApiDocsBuilderPlugin::make();

        $fontFamily = null;
        $fontUrl = null;
        $fontProvider = null;

        if ($settings?->enable_custom_font) {
            $fontFamily = $settings->font_family ?: 'Vazirmatn';
            $fontSource = $settings->font_source ?? 'bunny';

            if ($fontSource === 'url' && filled($settings->font_url)) {
                $fontUrl = $settings->font_url;
                $fontProvider = LocalFontProvider::class;
            } elseif (in_array($fontSource, ['upload_css', 'upload_file'], true) && filled($settings->font_upload_css_path)) {
                $fontUrl = asset('storage/' . ltrim($settings->font_upload_css_path, '/'));
                $fontProvider = LocalFontProvider::class;
            }
        }

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->font($fontFamily, $fontUrl, $fontProvider)
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->navigationGroups([
                'مگا سوپر ادمین',
                'مدیریت کلان',
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
            ->widgets([
                PanelInfoWidget::class,
            ])
            ->plugins($plugins)
            ->renderHook(PanelsRenderHook::SIDEBAR_NAV_START, fn () => view('filamat-iam::components.sidebar-role'))
            ->renderHook(PanelsRenderHook::GLOBAL_SEARCH_BEFORE, function () use ($resolveSettings) {
                $settings = $resolveSettings();
                if (! $settings?->topbar_date_enabled) {
                    return null;
                }

                $calendar = $settings->topbar_primary_calendar ?? 'jalali';
                if (! in_array($calendar, ['jalali', 'gregorian', 'hijri'], true)) {
                    $calendar = 'jalali';
                }

                $formatter = app(CalendarFormatter::class);
                $now = now();

                $tooltipLines = [];
                $toggles = [
                    'jalali' => (bool) ($settings->topbar_show_jalali ?? true),
                    'gregorian' => (bool) ($settings->topbar_show_gregorian ?? true),
                    'hijri' => (bool) ($settings->topbar_show_hijri ?? true),
                ];

                foreach ($toggles as $type => $enabled) {
                    if (! $enabled || $type === $calendar) {
                        continue;
                    }

                    $label = match ($type) {
                        'jalali' => 'شمسی',
                        'gregorian' => 'میلادی',
                        'hijri' => 'قمری',
                        default => 'تاریخ',
                    };

                    $tooltipLines[] = $label . ': ' . $formatter->formatDayDate($now, $type);
                }

                $tooltip = $tooltipLines ? implode('<br>', array_map('e', $tooltipLines)) : null;
                $label = $formatter->formatDayDate($now, $calendar);

                return view('filament.components.topbar-date', [
                    'label' => $label,
                    'tooltip' => $tooltip,
                ]);
            })
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
