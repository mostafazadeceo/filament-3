<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.calendar_display_mode', 'jalali');
        $this->migrator->add('general.topbar_date_enabled', true);
        $this->migrator->add('general.topbar_primary_calendar', 'jalali');
        $this->migrator->add('general.topbar_show_jalali', true);
        $this->migrator->add('general.topbar_show_gregorian', true);
        $this->migrator->add('general.topbar_show_hijri', true);
    }
};
