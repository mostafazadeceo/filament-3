<?php

namespace Haida\FilamentWorkhub\Database\Seeders;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentWorkhub\Models\Status;
use Haida\FilamentWorkhub\Models\Transition;
use Haida\FilamentWorkhub\Models\WorkType;
use Haida\FilamentWorkhub\Models\Workflow;
use Illuminate\Database\Seeder;

class WorkhubSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::query()->get();
        if ($tenants->isEmpty()) {
            return;
        }

        foreach ($tenants as $tenant) {
            TenantContext::setTenant($tenant);
            TenantContext::bypass(false);

            $workflow = Workflow::query()->firstOrCreate(
                ['tenant_id' => $tenant->getKey(), 'is_default' => true],
                [
                    'name' => config('filament-workhub.workflow.default_name', 'گردش‌کار پیش‌فرض'),
                    'description' => 'گردش‌کار اولیه Workhub',
                ]
            );

            if (! $workflow->statuses()->exists()) {
                $defaultStatuses = (array) config('filament-workhub.workflow.default_statuses', []);
                foreach ($defaultStatuses as $statusData) {
                    Status::query()->create(array_merge($statusData, [
                        'tenant_id' => $tenant->getKey(),
                        'workflow_id' => $workflow->getKey(),
                    ]));
                }
            }

            if (! $workflow->transitions()->exists()) {
                $statuses = $workflow->statuses()->orderBy('sort_order')->get();
                $todo = $statuses->firstWhere('category', 'todo');
                $inProgress = $statuses->firstWhere('category', 'in_progress');
                $done = $statuses->firstWhere('category', 'done');

                if ($todo && $inProgress) {
                    Transition::query()->create([
                        'tenant_id' => $tenant->getKey(),
                        'workflow_id' => $workflow->getKey(),
                        'name' => 'شروع انجام',
                        'from_status_id' => $todo->getKey(),
                        'to_status_id' => $inProgress->getKey(),
                        'sort_order' => 10,
                    ]);
                }

                if ($inProgress && $done) {
                    Transition::query()->create([
                        'tenant_id' => $tenant->getKey(),
                        'workflow_id' => $workflow->getKey(),
                        'name' => 'اتمام',
                        'from_status_id' => $inProgress->getKey(),
                        'to_status_id' => $done->getKey(),
                        'sort_order' => 20,
                    ]);
                }
            }

            if (! WorkType::query()->exists()) {
                WorkType::query()->create([
                    'tenant_id' => $tenant->getKey(),
                    'name' => 'کار',
                    'slug' => 'task',
                    'color' => '#3b82f6',
                    'is_active' => true,
                    'sort_order' => 10,
                ]);

                WorkType::query()->create([
                    'tenant_id' => $tenant->getKey(),
                    'name' => 'باگ',
                    'slug' => 'bug',
                    'color' => '#ef4444',
                    'is_active' => true,
                    'sort_order' => 20,
                ]);

                WorkType::query()->create([
                    'tenant_id' => $tenant->getKey(),
                    'name' => 'بهبود',
                    'slug' => 'improvement',
                    'color' => '#10b981',
                    'is_active' => true,
                    'sort_order' => 30,
                ]);
            }
        }

        TenantContext::setTenant(null);
    }
}
