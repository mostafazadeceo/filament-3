<?php

namespace Tests\Feature\ProvidersCore;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\ProvidersCore\Jobs\ProviderActionJob;
use Haida\ProvidersCore\Models\ProviderJobLog;
use Haida\ProvidersCore\Services\ProviderJobReprocessService;
use Haida\ProvidersCore\Support\ProviderAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ProviderJobReprocessTest extends TestCase
{
    use RefreshDatabase;

    public function test_reprocess_creates_new_log_and_dispatches_job(): void
    {
        Queue::fake();

        $tenant = Tenant::query()->create([
            'name' => 'Tenant Provider',
            'slug' => 'tenant-provider',
            'status' => 'active',
        ]);

        TenantContext::setTenant($tenant);

        $log = ProviderJobLog::query()->create([
            'tenant_id' => $tenant->getKey(),
            'provider_key' => 'dummy',
            'job_type' => ProviderAction::SyncProducts->value,
            'status' => 'failed',
            'connection_id' => null,
            'attempts' => 1,
            'payload' => ['source' => 'test'],
        ]);

        $service = app(ProviderJobReprocessService::class);
        $newLog = $service->reprocess($log);

        $this->assertSame('pending', $newLog->status);
        $this->assertSame($log->provider_key, $newLog->provider_key);
        $this->assertSame($log->job_type, $newLog->job_type);

        Queue::assertPushed(ProviderActionJob::class);
    }
}
