<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public bool $enable_auth_ui_enhancer = true;
    public ?string $auth_ui_empty_panel_background_image_path = null;
    public int $auth_ui_empty_panel_background_image_opacity = 100;
    public string $auth_ui_form_panel_position = 'right';
    public string $auth_ui_mobile_form_panel_position = 'top';
    public string $auth_ui_form_panel_width = '50%';
    public bool $auth_ui_show_empty_panel_on_mobile = true;
    public bool $enable_custom_font = true;
    public string $font_family = 'Vazirmatn';
    public string $font_source = 'local';
    public ?string $font_url = null;
    public ?string $font_upload_css_path = null;
    public ?string $font_upload_file_path = null;
    public int $font_upload_weight = 400;
    public string $font_upload_style = 'normal';
    public string $calendar_display_mode = 'jalali';
    public bool $topbar_date_enabled = true;
    public string $topbar_primary_calendar = 'jalali';
    public bool $topbar_show_jalali = true;
    public bool $topbar_show_gregorian = true;
    public bool $topbar_show_hijri = true;
    public bool $panel_info_widget_enabled = true;
    public ?string $panel_info_title = null;
    public ?string $panel_info_logo_path = null;
    public ?string $panel_info_logo_link = null;
    public bool $panel_info_show_version = true;
    public ?string $panel_info_version_text = null;
    public bool $panel_info_first_link_enabled = false;
    public string $panel_info_first_link_label = 'مستندات';
    public ?string $panel_info_first_link_url = null;
    public bool $panel_info_second_link_enabled = false;
    public string $panel_info_second_link_label = 'گیت‌هاب';
    public ?string $panel_info_second_link_url = null;

    public static function group(): string
    {
        return 'general';
    }
}
