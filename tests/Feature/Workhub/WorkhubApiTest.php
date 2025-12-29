<?php

namespace Tests\Feature\Workhub;

use Haida\FilamentWorkhub\Models\CustomField;
use Laravel\Sanctum\Sanctum;

class WorkhubApiTest extends WorkhubTestCase
{
    public function test_api_can_create_work_item_with_custom_fields_and_transition(): void
    {
        $tenant = $this->createTenant('Tenant API');
        $workflowData = $this->createWorkflowWithStatuses($tenant);

        CustomField::query()->create([
            'tenant_id' => $tenant->getKey(),
            'scope' => 'work_item',
            'name' => 'Impact',
            'key' => 'impact',
            'type' => 'select',
            'settings' => ['options' => ['low' => 'Low', 'high' => 'High']],
            'is_required' => false,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $user = $this->createUserWithPermissions($tenant, [
            'workhub.project.manage',
            'workhub.project.view',
            'workhub.work_item.manage',
            'workhub.work_item.view',
            'workhub.transition.manage',
            'workhub.transition.view',
        ]);

        Sanctum::actingAs($user, [
            'workhub.project.manage',
            'workhub.project.view',
            'workhub.work_item.manage',
            'workhub.work_item.view',
            'workhub.transition.manage',
            'workhub.transition.view',
            'tenant:'.$tenant->getKey(),
        ]);

        $projectResponse = $this->postJson('/api/v1/workhub/projects', [
            'key' => 'API',
            'name' => 'API Project',
            'workflow_id' => $workflowData['workflow']->getKey(),
        ], [
            'X-Tenant-ID' => $tenant->getKey(),
        ]);

        $projectResponse->assertCreated();
        $projectId = $projectResponse->json('data.id');

        $workItemResponse = $this->postJson('/api/v1/workhub/work-items', [
            'project_id' => $projectId,
            'title' => 'API Work Item',
            'priority' => 'medium',
            'custom_fields' => [
                'impact' => 'high',
            ],
        ], [
            'X-Tenant-ID' => $tenant->getKey(),
        ]);

        $workItemResponse->assertCreated();
        $workItemResponse->assertJsonPath('data.custom_fields.impact', 'high');

        $workItemId = $workItemResponse->json('data.id');

        $transitionResponse = $this->postJson('/api/v1/workhub/work-items/'.$workItemId.'/transition', [
            'to_status_id' => $workflowData['done']->getKey(),
        ], [
            'X-Tenant-ID' => $tenant->getKey(),
        ]);

        $transitionResponse->assertOk();
        $transitionResponse->assertJsonPath('data.status_id', $workflowData['done']->getKey());
    }
}
