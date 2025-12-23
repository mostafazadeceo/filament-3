<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.panel_info_widget_enabled', true);
        $this->migrator->add('general.panel_info_title', null);
        $this->migrator->add('general.panel_info_logo_path', null);
        $this->migrator->add('general.panel_info_logo_link', 'https://filamentphp.com');
        $this->migrator->add('general.panel_info_show_version', true);
        $this->migrator->add('general.panel_info_version_text', null);
        $this->migrator->add('general.panel_info_first_link_enabled', true);
        $this->migrator->add('general.panel_info_first_link_label', 'مستندات');
        $this->migrator->add('general.panel_info_first_link_url', 'https://filamentphp.com/docs');
        $this->migrator->add('general.panel_info_second_link_enabled', true);
        $this->migrator->add('general.panel_info_second_link_label', 'گیت‌هاب');
        $this->migrator->add('general.panel_info_second_link_url', 'https://github.com/filamentphp/filament');
    }
};
