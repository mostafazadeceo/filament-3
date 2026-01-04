<?php

namespace Tests\Feature\Meetings;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentAiCore\Models\AiPolicy;
use Haida\FilamentMeetings\Models\Meeting;
use Illuminate\Support\Facades\Config;
use Laravel\Sanctum\Sanctum;

class MeetingsAiTest extends MeetingsTestCase
{
    public function test_tenant_isolation_blocks_cross_tenant_access(): void
    {
        $tenantA = $this->createTenant('Tenant A');
        $tenantB = $this->createTenant('Tenant B');

        $user = $this->createUserWithPermissions($tenantA, [
            'meetings.view',
        ]);

        Sanctum::actingAs($user, [
            'meetings.view',
            'tenant:'.$tenantA->getKey(),
        ]);

        $meeting = Meeting::query()->create([
            'tenant_id' => $tenantB->getKey(),
            'title' => 'جلسه محرمانه',
            'status' => 'draft',
        ]);

        $response = $this->getJson('/api/v1/meetings/'.$meeting->getKey(), [
            'X-Tenant-ID' => $tenantA->getKey(),
        ]);

        $response->assertNotFound();
    }

    public function test_consent_required_blocks_minutes_generation_until_confirmed(): void
    {
        Config::set('filament-ai-core.enabled', true);
        Config::set('filament-ai-core.default_provider', 'mock');
        Config::set('filament-ai-core.allow_store_transcripts', true);

        $tenant = $this->createTenant('Tenant Meetings');
        TenantContext::setTenant($tenant);

        AiPolicy::query()->create([
            'tenant_id' => $tenant->getKey(),
            'enabled' => true,
            'provider' => 'mock',
            'allow_store_transcripts' => true,
            'consent_required_meetings' => true,
        ]);

        $user = $this->createUserWithPermissions($tenant, [
            'meetings.view',
            'meetings.manage',
            'meetings.ai.use',
            'meetings.transcript.manage',
            'meetings.minutes.manage',
            'meetings.action_items.manage',
        ]);

        Sanctum::actingAs($user, [
            'meetings.view',
            'meetings.manage',
            'meetings.ai.use',
            'meetings.transcript.manage',
            'meetings.minutes.manage',
            'meetings.action_items.manage',
            'tenant:'.$tenant->getKey(),
        ]);

        $meeting = Meeting::query()->create([
            'tenant_id' => $tenant->getKey(),
            'title' => 'جلسه محصول',
            'organizer_id' => $user->getKey(),
            'status' => 'scheduled',
            'ai_enabled' => true,
            'consent_required' => true,
            'consent_mode' => 'manual',
        ]);

        $blocked = $this->postJson('/api/v1/meetings/'.$meeting->getKey().'/ai/generate-minutes', [], [
            'X-Tenant-ID' => $tenant->getKey(),
        ]);

        $blocked->assertStatus(422);

        $consent = $this->postJson('/api/v1/meetings/'.$meeting->getKey().'/consent/confirm', [], [
            'X-Tenant-ID' => $tenant->getKey(),
        ]);

        $consent->assertOk();

        $transcript = $this->postJson('/api/v1/meetings/'.$meeting->getKey().'/transcript/manual', [
            'content' => "[00:01] علی: شروع جلسه\n[00:05] سارا: مرور اهداف",
            'language' => 'fa',
        ], [
            'X-Tenant-ID' => $tenant->getKey(),
        ]);

        $transcript->assertOk();

        $minutes = $this->postJson('/api/v1/meetings/'.$meeting->getKey().'/ai/generate-minutes', [], [
            'X-Tenant-ID' => $tenant->getKey(),
        ]);

        $minutes->assertOk();
        $minutes->assertJsonPath('ok', true);

        $this->assertDatabaseHas('meeting_minutes', [
            'tenant_id' => $tenant->getKey(),
            'meeting_id' => $meeting->getKey(),
        ]);
    }

    public function test_agenda_generation_creates_items(): void
    {
        Config::set('filament-ai-core.enabled', true);
        Config::set('filament-ai-core.default_provider', 'mock');

        $tenant = $this->createTenant('Tenant Agenda');
        TenantContext::setTenant($tenant);

        AiPolicy::query()->create([
            'tenant_id' => $tenant->getKey(),
            'enabled' => true,
            'provider' => 'mock',
        ]);

        $user = $this->createUserWithPermissions($tenant, [
            'meetings.view',
            'meetings.manage',
            'meetings.ai.use',
        ]);

        Sanctum::actingAs($user, [
            'meetings.view',
            'meetings.manage',
            'meetings.ai.use',
            'tenant:'.$tenant->getKey(),
        ]);

        $meeting = Meeting::query()->create([
            'tenant_id' => $tenant->getKey(),
            'title' => 'جلسه برنامه‌ریزی',
            'organizer_id' => $user->getKey(),
            'status' => 'draft',
            'ai_enabled' => true,
            'consent_required' => true,
            'consent_mode' => 'manual',
        ]);

        $response = $this->postJson('/api/v1/meetings/'.$meeting->getKey().'/ai/generate-agenda', [], [
            'X-Tenant-ID' => $tenant->getKey(),
        ]);

        $response->assertOk();
        $response->assertJsonPath('ok', true);

        $this->assertDatabaseHas('meeting_agenda_items', [
            'tenant_id' => $tenant->getKey(),
            'meeting_id' => $meeting->getKey(),
        ]);
    }

    public function test_link_action_items_to_workhub(): void
    {
        Config::set('filament-ai-core.enabled', true);

        $tenant = $this->createTenant('Tenant Links');
        TenantContext::setTenant($tenant);

        $workflowData = $this->createWorkflowWithStatuses($tenant);
        $project = $this->createProject($tenant, $workflowData['workflow']);

        $user = $this->createUserWithPermissions($tenant, [
            'meetings.view',
            'meetings.manage',
            'meetings.action_items.manage',
            'workhub.work_item.manage',
            'workhub.work_item.view',
            'workhub.project.view',
        ]);

        Sanctum::actingAs($user, [
            'meetings.view',
            'meetings.manage',
            'meetings.action_items.manage',
            'workhub.work_item.manage',
            'workhub.work_item.view',
            'workhub.project.view',
            'tenant:'.$tenant->getKey(),
        ]);

        $meeting = Meeting::query()->create([
            'tenant_id' => $tenant->getKey(),
            'title' => 'جلسه اقدام‌ها',
            'organizer_id' => $user->getKey(),
            'status' => 'completed',
            'ai_enabled' => false,
            'meta' => [
                'workhub_project_id' => $project->getKey(),
            ],
        ]);

        $actionItem = $this->createMeetingActionItem([
            'tenant_id' => $tenant->getKey(),
            'meeting_id' => $meeting->getKey(),
            'title' => 'تهیه پروپوزال',
            'description' => 'ارسال نسخه اول تا پایان هفته.',
            'status' => 'open',
        ]);

        $response = $this->postJson('/api/v1/meetings/'.$meeting->getKey().'/action-items/link-to-workhub', [
            'action_item_ids' => [$actionItem->getKey()],
            'project_id' => $project->getKey(),
        ], [
            'X-Tenant-ID' => $tenant->getKey(),
        ]);

        $response->assertOk();
        $response->assertJsonPath('ok', true);

        $actionItem->refresh();
        $this->assertNotNull($actionItem->linked_workhub_item_id);

        $this->assertDatabaseHas('meeting_action_items', [
            'id' => $actionItem->getKey(),
            'linked_workhub_item_id' => $actionItem->linked_workhub_item_id,
        ]);

        $this->assertDatabaseHas('workhub_work_items', [
            'tenant_id' => $tenant->getKey(),
            'project_id' => $project->getKey(),
            'title' => 'تهیه پروپوزال',
        ]);
    }

    public function test_ai_endpoints_require_permission(): void
    {
        Config::set('filament-ai-core.enabled', true);

        $tenant = $this->createTenant('Tenant AI Perm');
        TenantContext::setTenant($tenant);

        AiPolicy::query()->create([
            'tenant_id' => $tenant->getKey(),
            'enabled' => true,
            'provider' => 'mock',
        ]);

        $user = $this->createUserWithPermissions($tenant, [
            'meetings.view',
        ]);

        Sanctum::actingAs($user, [
            'meetings.view',
            'tenant:'.$tenant->getKey(),
        ]);

        $meeting = Meeting::query()->create([
            'tenant_id' => $tenant->getKey(),
            'title' => 'جلسه بدون دسترسی',
            'organizer_id' => $user->getKey(),
            'status' => 'draft',
            'ai_enabled' => true,
        ]);

        $response = $this->postJson('/api/v1/meetings/'.$meeting->getKey().'/ai/generate-agenda', [], [
            'X-Tenant-ID' => $tenant->getKey(),
        ]);

        $response->assertForbidden();
    }
}
