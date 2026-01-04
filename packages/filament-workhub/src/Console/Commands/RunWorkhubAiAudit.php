<?php

namespace Haida\FilamentWorkhub\Console\Commands;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentNotify\Core\Support\Triggers\TriggerDispatcher;
use Haida\FilamentWorkhub\Models\Project;
use Haida\FilamentWorkhub\Services\WorkhubAiService;
use Illuminate\Console\Command;

class RunWorkhubAiAudit extends Command
{
    protected $signature = 'workhub:ai:audit {--tenant=} {--days=30}';

    protected $description = 'تولید گزارش‌های ریسک و اقدامات معوق با هوش مصنوعی برای Workhub';

    public function handle(WorkhubAiService $service): int
    {
        $tenantOption = $this->option('tenant');
        $days = (int) $this->option('days');

        if ($tenantOption) {
            $this->runForTenant((int) $tenantOption, $service, $days);
            $this->info('گزارش هوش مصنوعی برای تننت اجرا شد.');

            return self::SUCCESS;
        }

        Tenant::query()
            ->select('id')
            ->orderBy('id')
            ->chunk(200, function ($tenants) use ($service, $days) {
                foreach ($tenants as $tenant) {
                    $this->runForTenant((int) $tenant->getKey(), $service, $days);
                }
            });

        $this->info('گزارش‌های هوش مصنوعی اجرا شد.');

        return self::SUCCESS;
    }

    protected function runForTenant(int $tenantId, WorkhubAiService $service, int $days): void
    {
        $tenant = Tenant::query()->find($tenantId);
        if (! $tenant) {
            return;
        }

        TenantContext::setTenant($tenant);

        try {
            Project::query()
                ->where('status', 'active')
                ->select('id')
                ->orderBy('id')
                ->chunk(100, function ($projects) use ($service, $days) {
                    foreach ($projects as $project) {
                        $record = Project::query()->find($project->getKey());
                        if (! $record) {
                            continue;
                        }

                        $payload = $service->generateExecutiveSummary($record, [
                            'updated_since_days' => $days,
                        ]);

                        if (! $payload['result']->ok) {
                            continue;
                        }

                        $this->dispatchNotifications($record);
                    }
                });
        } finally {
            TenantContext::setTenant(null);
        }
    }

    protected function dispatchNotifications(Project $project): void
    {
        if (! class_exists(TriggerDispatcher::class)) {
            return;
        }

        $panelId = (string) config('filament-workhub.notifications.panel', 'tenant');
        if ($panelId === '') {
            return;
        }

        try {
            app(TriggerDispatcher::class)->dispatchForEloquent($panelId, $project, 'workhub.ai.project_report.created');
        } catch (\Throwable) {
            // keep audits resilient
        }
    }
}
