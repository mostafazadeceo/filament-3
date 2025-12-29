<?php

namespace Tests\Feature\Workhub;

use App\Models\User;
use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentWorkhub\Models\Label;
use Haida\FilamentWorkhub\Models\Status;
use Haida\FilamentWorkhub\Models\Workflow;
use Haida\FilamentWorkhub\Models\WorkType;
use Laravel\Sanctum\Sanctum;

class WorkhubValidationTest extends WorkhubTestCase
{
    public function test_work_item_rejects_status_outside_project_workflow(): void
    {
        $tenant = $this->createTenant('Tenant Validation');
        $workflowData = $this->createWorkflowWithStatuses($tenant);
        $project = $this->createProject($tenant, $workflowData['workflow']);

        $otherWorkflow = Workflow::query()->create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'Secondary Workflow',
            'is_default' => false,
        ]);

        $otherStatus = Status::query()->create([
            'tenant_id' => $tenant->getKey(),
            'workflow_id' => $otherWorkflow->getKey(),
            'name' => 'Other',
            'slug' => 'other-status',
            'category' => 'todo',
            'sort_order' => 1,
            'is_default' => true,
        ]);

        $user = $this->createUserWithPermissions($tenant, [
            'workhub.work_item.manage',
        ]);

        Sanctum::actingAs($user, [
            'workhub.work_item.manage',
            'tenant:' . $tenant->getKey(),
        ]);

        $response = $this->postJson('/api/v1/workhub/work-items', [
            'project_id' => $project->getKey(),
            'title' => 'Invalid Status',
            'priority' => 'medium',
            'status_id' => $otherStatus->getKey(),
        ], [
            'X-Tenant-ID' => $tenant->getKey(),
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('status_id');
    }

    public function test_work_item_rejects_cross_tenant_labels_and_work_type(): void
    {
        $tenantA = $this->createTenant('Tenant A');
        $workflowData = $this->createWorkflowWithStatuses($tenantA);
        $project = $this->createProject($tenantA, $workflowData['workflow']);

        $tenantB = $this->createTenant('Tenant B');
        TenantContext::setTenant($tenantB);

        $label = Label::query()->create([
            'tenant_id' => $tenantB->getKey(),
            'name' => 'B Label',
            'slug' => 'b-label',
            'color' => '#000000',
        ]);

        $workType = WorkType::query()->create([
            'tenant_id' => $tenantB->getKey(),
            'name' => 'B Type',
            'slug' => 'b-type',
            'is_active' => true,
        ]);

        $user = $this->createUserWithPermissions($tenantA, [
            'workhub.work_item.manage',
        ]);

        Sanctum::actingAs($user, [
            'workhub.work_item.manage',
            'tenant:' . $tenantA->getKey(),
        ]);

        $response = $this->postJson('/api/v1/workhub/work-items', [
            'project_id' => $project->getKey(),
            'title' => 'Cross Tenant',
            'priority' => 'medium',
            'labels' => [$label->getKey()],
            'work_type_id' => $workType->getKey(),
        ], [
            'X-Tenant-ID' => $tenantA->getKey(),
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['labels', 'work_type_id']);
    }

    public function test_watcher_requires_tenant_member(): void
    {
        $tenant = $this->createTenant('Tenant Watcher');
        $workflowData = $this->createWorkflowWithStatuses($tenant);
        $project = $this->createProject($tenant, $workflowData['workflow']);

        $workItem = app(\Haida\FilamentWorkhub\Services\WorkItemCreator::class)->create([
            'tenant_id' => $tenant->getKey(),
            'project_id' => $project->getKey(),
            'title' => 'Watcher Item',
            'priority' => 'medium',
        ]);

        $member = $this->createUserWithPermissions($tenant, [
            'workhub.watcher.manage',
        ]);

        $outsider = User::factory()->create();

        Sanctum::actingAs($member, [
            'workhub.watcher.manage',
            'tenant:' . $tenant->getKey(),
        ]);

        $response = $this->postJson('/api/v1/workhub/work-items/' . $workItem->getKey() . '/watchers', [
            'user_id' => $outsider->getKey(),
        ], [
            'X-Tenant-ID' => $tenant->getKey(),
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('user_id');
    }
}
