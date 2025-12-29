<?php

namespace Haida\FilamentWorkhub\Console\Commands;

use Filamat\IamSuite\Models\Tenant;
use Haida\FilamentWorkhub\Services\WorkhubAutomationEngine;
use Illuminate\Console\Command;

class RunWorkhubAutomation extends Command
{
    protected $signature = 'workhub:automation:run {--tenant=}';

    protected $description = 'اجرای قوانین زمان‌بندی‌شده اتوماسیون Workhub';

    public function handle(WorkhubAutomationEngine $engine): int
    {
        $tenantOption = $this->option('tenant');
        if ($tenantOption) {
            $engine->runScheduled((int) $tenantOption);
            $this->info('اتوماسیون برای تننت اجرا شد.');

            return self::SUCCESS;
        }

        Tenant::query()
            ->select('id')
            ->orderBy('id')
            ->chunk(200, function ($tenants) use ($engine) {
                foreach ($tenants as $tenant) {
                    $engine->runScheduled((int) $tenant->getKey());
                }
            });

        $this->info('اتوماسیون زمان‌بندی‌شده اجرا شد.');

        return self::SUCCESS;
    }
}
