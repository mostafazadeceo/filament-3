<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Console\Commands;

use Filamat\IamSuite\Services\CapabilitySyncService;
use Illuminate\Console\Command;

class SyncCapabilitiesCommand extends Command
{
    protected $signature = 'filamat-iam:sync {--guard=web}';

    protected $description = 'همگام‌سازی قابلیت‌ها و مجوزها';

    public function handle(CapabilitySyncService $service): int
    {
        $guard = (string) $this->option('guard');
        $count = $service->sync($guard);

        $this->info("{$count} مجوز همگام شد.");

        return self::SUCCESS;
    }
}
