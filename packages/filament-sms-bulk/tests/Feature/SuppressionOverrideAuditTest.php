<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Tests\Feature;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\SmsBulk\Jobs\EnqueueCampaignJob;
use Haida\SmsBulk\Models\SmsBulkAuditLog;
use Haida\SmsBulk\Models\SmsBulkCampaign;
use Haida\SmsBulk\Models\SmsBulkProviderConnection;
use Haida\SmsBulk\Models\SmsBulkSuppressionList;
use Haida\SmsBulk\Tests\TestCase;

class SuppressionOverrideAuditTest extends TestCase
{
    public function test_override_suppression_writes_audit_log(): void
    {
        $tenant = Tenant::create(['name' => 'Tenant', 'slug' => 'tenant-audit']);
        TenantContext::setTenant($tenant);

        $connection = SmsBulkProviderConnection::create([
            'tenant_id' => $tenant->getKey(),
            'provider' => 'ippanel_edge',
            'display_name' => 'edge',
            'status' => 'active',
        ]);

        SmsBulkSuppressionList::create([
            'tenant_id' => $tenant->getKey(),
            'msisdn' => '09120000002',
            'source' => 'manual',
        ]);

        $campaign = SmsBulkCampaign::create([
            'tenant_id' => $tenant->getKey(),
            'provider_connection_id' => $connection->getKey(),
            'name' => 'Override Campaign',
            'mode' => 'standard',
            'language' => 'fa',
            'encoding' => 'auto',
            'sender' => '3000505',
            'approval_state' => 'approved',
            'status' => 'draft',
            'payload_snapshot' => [
                'message' => 'hello',
                'recipients' => ['09120000001', '09120000002'],
                'override_suppression' => true,
            ],
            'idempotency_key' => 'override-key',
        ]);

        EnqueueCampaignJob::dispatchSync($tenant->getKey(), $campaign->getKey());

        $this->assertTrue(SmsBulkAuditLog::query()
            ->where('tenant_id', $tenant->getKey())
            ->where('action', 'campaign.suppression.override')
            ->exists());
    }
}
