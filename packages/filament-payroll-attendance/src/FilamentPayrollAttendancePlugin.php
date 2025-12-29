<?php

namespace Haida\FilamentPayrollAttendance;

use Filament\Contracts\Plugin;
use Filament\Panel;

class FilamentPayrollAttendancePlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'payroll-attendance';
    }

    public function register(Panel $panel): void
    {
        // Resources, pages, and widgets are registered in later milestones.
    }

    public function boot(Panel $panel): void
    {
        // No-op.
    }
}
