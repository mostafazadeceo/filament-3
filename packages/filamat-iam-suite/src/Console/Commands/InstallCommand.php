<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Console\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'filamat-iam:install {--force} {--seed}';

    protected $description = 'نصب و انتشار منابع Filamat IAM Suite';

    public function handle(): int
    {
        $force = (bool) $this->option('force');

        $this->call('vendor:publish', ['--tag' => 'filamat-iam-suite-config', '--force' => $force]);
        $this->call('vendor:publish', ['--tag' => 'filamat-iam-suite-migrations', '--force' => $force]);
        $this->call('vendor:publish', ['--tag' => 'filamat-iam-suite-views', '--force' => $force]);
        $this->call('vendor:publish', ['--tag' => 'filamat-iam-suite-translations', '--force' => $force]);

        $this->call('migrate', ['--force' => $force]);

        if ($this->option('seed')) {
            $this->call('db:seed', ['--class' => 'Filamat\\IamSuite\\Database\\Seeders\\FilamatIamSeeder']);
        }

        $this->info('نصب کامل شد.');

        return self::SUCCESS;
    }
}
