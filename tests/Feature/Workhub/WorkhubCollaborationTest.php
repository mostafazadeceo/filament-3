<?php

namespace Tests\Feature\Workhub;

use Haida\FilamentWorkhub\Services\WorkItemCreator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

class WorkhubCollaborationTest extends WorkhubTestCase
{
    public function test_collaboration_endpoints_work(): void
    {
        Storage::fake('public');

        $tenant = $this->createTenant('Tenant Collaboration');
        $workflowData = $this->createWorkflowWithStatuses($tenant);
        $project = $this->createProject($tenant, $workflowData['workflow']);

        $workItem = app(WorkItemCreator::class)->create([
            'tenant_id' => $tenant->getKey(),
            'project_id' => $project->getKey(),
            'title' => 'Collaboration Item',
            'priority' => 'medium',
        ]);

        $user = $this->createUserWithPermissions($tenant, [
            'workhub.comment.manage',
            'workhub.attachment.manage',
            'workhub.watcher.manage',
            'workhub.time_entry.manage',
            'workhub.decision.manage',
        ]);

        Sanctum::actingAs($user, [
            'workhub.comment.manage',
            'workhub.attachment.manage',
            'workhub.watcher.manage',
            'workhub.time_entry.manage',
            'workhub.decision.manage',
            'tenant:' . $tenant->getKey(),
        ]);

        $commentResponse = $this->postJson('/api/v1/workhub/work-items/' . $workItem->getKey() . '/comments', [
            'body' => 'یادداشت تست',
        ], [
            'X-Tenant-ID' => $tenant->getKey(),
        ]);

        $commentResponse->assertSuccessful();

        $watcherResponse = $this->postJson('/api/v1/workhub/work-items/' . $workItem->getKey() . '/watchers', [
            'user_id' => $user->getKey(),
        ], [
            'X-Tenant-ID' => $tenant->getKey(),
        ]);

        $watcherResponse->assertSuccessful();

        $timeEntryResponse = $this->postJson('/api/v1/workhub/work-items/' . $workItem->getKey() . '/time-entries', [
            'minutes' => 30,
            'note' => 'ثبت زمان',
        ], [
            'X-Tenant-ID' => $tenant->getKey(),
        ]);

        $timeEntryResponse->assertSuccessful();

        $decisionResponse = $this->postJson('/api/v1/workhub/work-items/' . $workItem->getKey() . '/decisions', [
            'title' => 'تصمیم تست',
            'body' => 'بدنه تصمیم',
        ], [
            'X-Tenant-ID' => $tenant->getKey(),
        ]);

        $decisionResponse->assertSuccessful();

        $file = UploadedFile::fake()->create('note.txt', 5, 'text/plain');
        $attachmentResponse = $this->postJson('/api/v1/workhub/work-items/' . $workItem->getKey() . '/attachments', [
            'file' => $file,
        ], [
            'X-Tenant-ID' => $tenant->getKey(),
        ]);

        $attachmentResponse->assertSuccessful();
        $path = $attachmentResponse->json('data.path');
        if (is_string($path)) {
            Storage::disk('public')->assertExists($path);
        }
    }
}
