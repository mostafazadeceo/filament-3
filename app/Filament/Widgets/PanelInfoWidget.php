<?php

namespace App\Filament\Widgets;

use App\Settings\GeneralSettings;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Schema;

class PanelInfoWidget extends Widget
{
    protected static ?int $sort = -2;

    protected static bool $isLazy = false;

    /**
     * @var view-string
     */
    protected string $view = 'filament.widgets.panel-info-widget';

    public static function canView(): bool
    {
        try {
            if (! Schema::hasTable('settings')) {
                return false;
            }
        } catch (\Throwable) {
            return false;
        }

        return (bool) (app(GeneralSettings::class)->panel_info_widget_enabled ?? true);
    }

    protected function getViewData(): array
    {
        $settings = app(GeneralSettings::class);

        $logoPath = $settings->panel_info_logo_path ?? null;
        $logoUrl = filled($logoPath) ? asset('storage/' . ltrim($logoPath, '/')) : null;

        $versionText = $settings->panel_info_version_text;
        if (! filled($versionText)) {
            $versionText = \Composer\InstalledVersions::getPrettyVersion('filament/filament');
        }

        return [
            'title' => $settings->panel_info_title,
            'logoUrl' => $logoUrl,
            'logoLink' => $settings->panel_info_logo_link,
            'showVersion' => (bool) ($settings->panel_info_show_version ?? true),
            'versionText' => $versionText,
            'firstLinkEnabled' => (bool) ($settings->panel_info_first_link_enabled ?? true),
            'firstLinkLabel' => $settings->panel_info_first_link_label,
            'firstLinkUrl' => $settings->panel_info_first_link_url,
            'secondLinkEnabled' => (bool) ($settings->panel_info_second_link_enabled ?? true),
            'secondLinkLabel' => $settings->panel_info_second_link_label,
            'secondLinkUrl' => $settings->panel_info_second_link_url,
        ];
    }
}
