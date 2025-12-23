<?php

namespace App\Providers\Filament;

use App\Settings\GeneralSettings;
use App\Support\Calendar\CalendarFormatter;
use DiogoGPinto\AuthUIEnhancer\AuthUIEnhancerPlugin;
use Haida\FilamentNotify\Core\FilamentNotifyPlugin;
use Filament\FontProviders\LocalFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
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
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                PanelInfoWidget::class,
            ])
            ->plugins($plugins)
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
