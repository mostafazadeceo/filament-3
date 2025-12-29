<?php

namespace Tests\Feature\Workhub;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentWorkhub\Models\WorkItem;

class WorkhubTenancyTest extends WorkhubTestCase
{
    public function test_work_items_are_scoped_by_tenant(): void
    {
        $tenantA = $this->createTenant('Tenant A');
        $tenantB = $this->createTenant('Tenant B');

        $workflowA = $this->createWorkflowWithStatuses($tenantA);
        $workflowB = $this->createWorkflowWithStatuses($tenantB);

        $projectA = $this->createProject($tenantA, $workflowA['workflow']);
        $projectB = $this->createProject($tenantB, $workflowB['workflow']);

        $itemA = WorkItem::query()->create([
            'tenant_id' => $tenantA->getKey(),
            'project_id' => $projectA->getKey(),
            'workflow_id' => $workflowA['workflow']->getKey(),
            'status_id' => $workflowA['todo']->getKey(),
            'number' => 1,
            'key' => 'A-1',
            'title' => 'Item A',
            'priority' => 'medium',
            'sort_order' => 1,
        ]);

        $itemB = WorkItem::query()->create([
            'tenant_id' => $tenantB->getKey(),
            'project_id' => $projectB->getKey(),
            'workflow_id' => $workflowB['workflow']->getKey(),
            'status_id' => $workflowB['todo']->getKey(),
            'number' => 1,
            'key' => 'B-1',
            'title' => 'Item B',
            'priority' => 'medium',
            'sort_order' => 1,
        ]);

        TenantContext::setTenant($tenantA);

        $this->assertTrue(WorkItem::query()->whereKey($itemA->getKey())->exists());
        $this->assertFalse(WorkItem::query()->whereKey($itemB->getKey())->exists());
        $this->assertSame(1, WorkItem::query()->count());
    }
}
