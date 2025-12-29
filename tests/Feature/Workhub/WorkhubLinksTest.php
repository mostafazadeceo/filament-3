<?php

namespace Tests\Feature\Workhub;

use Haida\FilamentWorkhub\Models\Project;
use Haida\FilamentWorkhub\Services\WorkItemCreator;
use Laravel\Sanctum\Sanctum;

class WorkhubLinksTest extends WorkhubTestCase
{
    public function test_links_respect_allowed_types(): void
    {
        $tenant = $this->createTenant('Tenant Links');
        $workflowData = $this->createWorkflowWithStatuses($tenant);

        $project = $this->createProject($tenant, $workflowData['workflow']);
        $project->forceFill(['allowed_link_types' => ['workhub.project']])->save();

        $otherProject = Project::query()->create([
            'tenant_id' => $tenant->getKey(),
            'workflow_id' => $workflowData['workflow']->getKey(),
            'key' => 'PRJ-LINK',
            'name' => 'Link Target',
            'status' => 'active',
        ]);

        $workItem = app(WorkItemCreator::class)->create([
            'tenant_id' => $tenant->getKey(),
            'project_id' => $project->getKey(),
            'title' => 'Work Item',
            'priority' => 'medium',
        ]);

        $otherWorkItem = app(WorkItemCreator::class)->create([
            'tenant_id' => $tenant->getKey(),
            'project_id' => $project->getKey(),
            'title' => 'Other Item',
            'priority' => 'low',
        ]);

        $user = $this->createUserWithPermissions($tenant, [
            'workhub.link.manage',
            'workhub.work_item.view',
        ]);

        Sanctum::actingAs($user, [
            'workhub.link.manage',
            'workhub.work_item.view',
            'tenant:' . $tenant->getKey(),
        ]);

        $allowedResponse = $this->postJson('/api/v1/workhub/work-items/' . $workItem->getKey() . '/links', [
            'target_type' => 'workhub.project',
            'target_id' => $otherProject->getKey(),
        ], [
            'X-Tenant-ID' => $tenant->getKey(),
        ]);

        $allowedResponse->assertSuccessful();

        $blockedResponse = $this->postJson('/api/v1/workhub/work-items/' . $workItem->getKey() . '/links', [
            'target_type' => 'workhub.work_item',
            'target_id' => $otherWorkItem->getKey(),
        ], [
            'X-Tenant-ID' => $tenant->getKey(),
        ]);

        $blockedResponse->assertStatus(422);
    }
}
