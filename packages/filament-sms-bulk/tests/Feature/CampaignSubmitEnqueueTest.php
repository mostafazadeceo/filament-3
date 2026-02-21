<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Tests\Feature;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\SmsBulk\Jobs\EnqueueCampaignJob;
use Haida\SmsBulk\Models\SmsBulkCampaign;
use Haida\SmsBulk\Models\SmsBulkProviderConnection;
use Haida\SmsBulk\Tests\Fixtures\User;
use Haida\SmsBulk\Tests\TestCase;
use Illuminate\Support\Facades\Bus;

class CampaignSubmitEnqueueTest extends TestCase
{
    public function test_submit_endpoint_enqueues_campaign_job(): void
    {
        Bus::fake();

        $tenant = Tenant::create(['name' => 'Tenant', 'slug' => 'tenant-job']);
        TenantContext::setTenant($tenant);

        $user = User::create(['name' => 'U', 'email' => 'u@example.test', 'password' => bcrypt('secret')]);
        $this->actingAs($user);

        $connection = SmsBulkProviderConnection::create([
            'tenant_id' => $tenant->getKey(),
            'provider' => 'ippanel_edge',
            'display_name' => 'edge',
            'status' => 'active',
        ]);

        $campaign = SmsBulkCampaign::create([
            'tenant_id' => $tenant->getKey(),
            'provider_connection_id' => $connection->getKey(),
            'name' => 'C1',
            'mode' => 'standard',
            'language' => 'fa',
            'encoding' => 'auto',
            'sender' => '3000505',
            'approval_state' => 'approved',
            'status' => 'draft',
            'payload_snapshot' => ['message' => 'hi', 'recipients' => ['09120000001']],
            'idempotency_key' => 'k1',
        ]);

        $this->withoutMiddleware()->postJson('/api/v1/sms-bulk/campaigns/'.$campaign->getKey().'/submit')
            ->assertOk();

        Bus::assertDispatched(EnqueueCampaignJob::class);
    }
}
