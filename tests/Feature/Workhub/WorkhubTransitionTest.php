<?php

namespace Tests\Feature\Workhub;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentWorkhub\Models\AuditEvent;
use Haida\FilamentWorkhub\Models\WorkItem;
use Haida\FilamentWorkhub\Services\WorkflowTransitionService;

class WorkhubTransitionTest extends WorkhubTestCase
{
    public function test_transition_updates_status_and_audit(): void
    {
        $tenant = $this->createTenant('Tenant Transition');
        $workflowData = $this->createWorkflowWithStatuses($tenant);
        $project = $this->createProject($tenant, $workflowData['workflow']);

        $user = $this->createUserWithPermissions($tenant, [
            'workhub.transition.manage',
        ]);

        TenantContext::setTenant($tenant);
        $this->actingAs($user);

        $workItem = WorkItem::query()->create([
            'tenant_id' => $tenant->getKey(),
            'project_id' => $project->getKey(),
            'workflow_id' => $workflowData['workflow']->getKey(),
            'status_id' => $workflowData['todo']->getKey(),
            'number' => 1,
            'key' => 'TR-1',
            'title' => 'Transition Item',
            'priority' => 'medium',
            'sort_order' => 1,
        ]);

        app(WorkflowTransitionService::class)->transition($workItem, $workflowData['done']);

        $workItem->refresh();

        $this->assertSame($workflowData['done']->getKey(), $workItem->status_id);
        $this->assertNotNull($workItem->completed_at);
        $this->assertTrue(
            AuditEvent::query()
                ->where('work_item_id', $workItem->getKey())
                ->where('event', 'work_item.transitioned')
                ->exists()
        );
    }
}
