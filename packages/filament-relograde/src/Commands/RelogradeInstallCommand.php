<?php

namespace Haida\FilamentRelograde\Commands;

use Illuminate\Console\Command;

class RelogradeInstallCommand extends Command
{
    protected $signature = 'relograde:install {--force : بازنویسی فایل‌های موجود} {--migrate : اجرای مایگریشن‌ها}';

    protected $description = 'نصب فایل‌ها و مایگریشن‌های افزونه رلوگرید.';

    public function handle(): int
    {
        $force = (bool) $this->option('force');

        $this->call('vendor:publish', [
            '--tag' => 'filament-relograde-config',
            '--force' => $force,
        ]);

        $this->call('vendor:publish', [
            '--tag' => 'filament-relograde-migrations',
            '--force' => $force,
        ]);

        if ($this->option('migrate')) {
            $this->call('migrate');
        }

        $this->info('افزونه رلوگرید نصب شد.');

        return self::SUCCESS;
    }
}
