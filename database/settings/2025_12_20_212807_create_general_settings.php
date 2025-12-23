<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.enable_auth_ui_enhancer', true);
        $this->migrator->add('general.auth_ui_empty_panel_background_image_path', null);
        $this->migrator->add('general.auth_ui_empty_panel_background_image_opacity', 100);
        $this->migrator->add('general.auth_ui_form_panel_position', 'right');
        $this->migrator->add('general.auth_ui_mobile_form_panel_position', 'top');
        $this->migrator->add('general.auth_ui_form_panel_width', '50%');
        $this->migrator->add('general.auth_ui_show_empty_panel_on_mobile', true);
        $this->migrator->add('general.enable_custom_font', true);
        $this->migrator->add('general.font_family', 'Vazirmatn');
        $this->migrator->add('general.font_source', 'bunny');
        $this->migrator->add('general.font_url', null);
        $this->migrator->add('general.font_upload_css_path', null);
        $this->migrator->add('general.font_upload_file_path', null);
        $this->migrator->add('general.font_upload_weight', 400);
        $this->migrator->add('general.font_upload_style', 'normal');
    }
};
