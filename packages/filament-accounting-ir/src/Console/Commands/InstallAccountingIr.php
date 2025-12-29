<?php

namespace Vendor\FilamentAccountingIr\Console\Commands;

use Illuminate\Console\Command;

class InstallAccountingIr extends Command
{
    protected $signature = 'filament-accounting:install {--migrate : اجرای مایگریشن‌ها} {--seed : اجرای سیدرهای پایه}';

    protected $description = 'نصب اولیه افزونه حسابداری ایران';

    public function handle(): int
    {
        $this->call('vendor:publish', [
            '--tag' => 'filament-accounting-ir-config',
            '--force' => false,
        ]);

        if ($this->option('migrate')) {
            $this->call('migrate');
        }

        if ($this->option('seed')) {
            $this->call('db:seed', [
                '--class' => \Vendor\FilamentAccountingIr\Database\Seeders\AccountingIrSeeder::class,
            ]);
        }

        $this->info('Accounting IR نصب شد.');

        return self::SUCCESS;
    }
}
