<?php

namespace Tests\Feature\Workhub;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentWorkhub\Models\AutomationRule;
use Haida\FilamentWorkhub\Services\WorkItemCreator;

class WorkhubAutomationTest extends WorkhubTestCase
{
    public function test_automation_runs_on_work_item_created(): void
    {
        $tenant = $this->createTenant('Tenant Automation');
        $workflowData = $this->createWorkflowWithStatuses($tenant);
        $project = $this->createProject($tenant, $workflowData['workflow']);

        AutomationRule::query()->create([
            'tenant_id' => $tenant->getKey(),
            'project_id' => $project->getKey(),
            'name' => 'Set priority high',
            'is_active' => true,
            'trigger_type' => 'work_item.created',
            'actions' => [
                [
                    'type' => 'set_priority',
                    'config' => [
                        'priority' => 'high',
                    ],
                ],
            ],
        ]);

        $user = $this->createUserWithPermissions($tenant, [
            'workhub.work_item.manage',
            'workhub.project.view',
        ]);

        TenantContext::setTenant($tenant);
        auth()->login($user);

        $workItem = app(WorkItemCreator::class)->create([
            'tenant_id' => $tenant->getKey(),
            'project_id' => $project->getKey(),
            'title' => 'Automation Item',
            'priority' => 'low',
        ]);

        $this->assertSame('high', $workItem->refresh()->priority);
    }
}
