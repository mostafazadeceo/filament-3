<?php

namespace Tests\Feature\Workhub;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentAiCore\Models\AiPolicy;
use Haida\FilamentWorkhub\Jobs\GenerateAiFieldBulkJob;
use Haida\FilamentWorkhub\Models\Comment;
use Haida\FilamentWorkhub\Models\CustomField;
use Haida\FilamentWorkhub\Models\WorkItem;
use Haida\FilamentWorkhub\Services\WorkItemCreator;
use Illuminate\Support\Facades\Config;
use Laravel\Sanctum\Sanctum;

class WorkhubAiTest extends WorkhubTestCase
{
    public function test_personal_and_shared_summaries_are_stored(): void
    {
        Config::set('filament-ai-core.enabled', true);
        Config::set('filament-ai-core.default_provider', 'mock');

        $tenant = $this->createTenant('Tenant AI');
        $workflowData = $this->createWorkflowWithStatuses($tenant);
        $project = $this->createProject($tenant, $workflowData['workflow']);

        TenantContext::setTenant($tenant);

        AiPolicy::query()->create([
            'tenant_id' => $tenant->getKey(),
            'enabled' => true,
            'provider' => 'mock',
        ]);

        $user = $this->createUserWithPermissions($tenant, [
            'workhub.work_item.manage',
            'workhub.work_item.view',
            'workhub.ai.use',
            'workhub.ai.share',
            'workhub.comment.manage',
        ]);

        Sanctum::actingAs($user, [
            'workhub.work_item.manage',
            'workhub.work_item.view',
            'workhub.ai.use',
            'workhub.ai.share',
            'tenant:'.$tenant->getKey(),
        ]);

        $workItem = app(WorkItemCreator::class)->create([
            'tenant_id' => $tenant->getKey(),
            'project_id' => $project->getKey(),
            'title' => 'AI Work Item',
            'priority' => 'medium',
            'reporter_id' => $user->getKey(),
        ]);

        Comment::query()->create([
            'tenant_id' => $tenant->getKey(),
            'work_item_id' => $workItem->getKey(),
            'user_id' => $user->getKey(),
            'body' => 'نمونه دیدگاه برای خلاصه.',
            'is_internal' => false,
        ]);

        $response = $this->postJson('/api/v1/workhub/work-items/'.$workItem->getKey().'/ai/personal-summary', [
            'include_comments' => true,
        ], [
            'X-Tenant-ID' => $tenant->getKey(),
        ]);

        $response->assertOk();
        $response->assertJsonPath('ok', true);

        $this->assertDatabaseHas('workhub_ai_summaries', [
            'tenant_id' => $tenant->getKey(),
            'work_item_id' => $workItem->getKey(),
            'type' => 'ttl',
        ]);

        $shared = $this->postJson('/api/v1/workhub/work-items/'.$workItem->getKey().'/ai/shared-summary', [
            'include_comments' => true,
        ], [
            'X-Tenant-ID' => $tenant->getKey(),
        ]);

        $shared->assertOk();
        $shared->assertJsonPath('ok', true);

        $this->assertDatabaseHas('workhub_ai_summaries', [
            'tenant_id' => $tenant->getKey(),
            'work_item_id' => $workItem->getKey(),
            'type' => 'shared',
        ]);
    }

    public function test_ai_endpoints_require_permissions(): void
    {
        Config::set('filament-ai-core.enabled', true);

        $tenant = $this->createTenant('Tenant AI Perm');
        $workflowData = $this->createWorkflowWithStatuses($tenant);
        $project = $this->createProject($tenant, $workflowData['workflow']);

        $user = $this->createUserWithPermissions($tenant, [
            'workhub.work_item.view',
        ]);

        Sanctum::actingAs($user, [
            'workhub.work_item.view',
            'tenant:'.$tenant->getKey(),
        ]);

        $workItem = app(WorkItemCreator::class)->create([
            'tenant_id' => $tenant->getKey(),
            'project_id' => $project->getKey(),
            'title' => 'AI No Perm',
            'priority' => 'medium',
            'reporter_id' => $user->getKey(),
        ]);

        $response = $this->postJson('/api/v1/workhub/work-items/'.$workItem->getKey().'/ai/personal-summary', [], [
            'X-Tenant-ID' => $tenant->getKey(),
        ]);

        $response->assertForbidden();
    }

    public function test_bulk_ai_field_job_generates_runs(): void
    {
        Config::set('filament-ai-core.enabled', true);
        Config::set('filament-ai-core.default_provider', 'mock');
        Config::set('queue.default', 'sync');

        $tenant = $this->createTenant('Tenant AI Fields');
        $workflowData = $this->createWorkflowWithStatuses($tenant);
        $project = $this->createProject($tenant, $workflowData['workflow']);

        TenantContext::setTenant($tenant);

        AiPolicy::query()->create([
            'tenant_id' => $tenant->getKey(),
            'enabled' => true,
            'provider' => 'mock',
        ]);

        $field = CustomField::query()->create([
            'tenant_id' => $tenant->getKey(),
            'scope' => 'work_item',
            'name' => 'AI Field',
            'key' => 'ai_field',
            'type' => 'ai_field',
            'settings' => [
                'prompt_template' => 'خلاصه: {{title}}',
                'output_schema' => json_encode(['type' => 'object', 'properties' => ['value' => ['type' => 'string']]]),
            ],
            'is_required' => false,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $items = [];
        for ($i = 0; $i < 2; $i++) {
            $items[] = app(WorkItemCreator::class)->create([
                'tenant_id' => $tenant->getKey(),
                'project_id' => $project->getKey(),
                'title' => 'AI Item '.$i,
                'priority' => 'medium',
            ]);
        }

        GenerateAiFieldBulkJob::dispatchSync($tenant->getKey(), $field->getKey(), null, 10);

        $this->assertDatabaseCount('workhub_ai_field_runs', 2);
        $this->assertDatabaseHas('workhub_custom_field_values', [
            'field_id' => $field->getKey(),
            'work_item_id' => $items[0]->getKey(),
        ]);
    }
}
