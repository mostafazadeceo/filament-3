<?php

namespace Haida\FilamentThreeCx\Tests\Feature;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentThreeCx\Models\ThreeCxApiAuditLog;
use Haida\FilamentThreeCx\Models\ThreeCxCallLog;
use Haida\FilamentThreeCx\Models\ThreeCxInstance;
use Haida\FilamentThreeCx\Models\ThreeCxSyncCursor;
use Haida\FilamentThreeCx\Tests\TestCase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ThreeCxPurgeCommandTest extends TestCase
{
    public function test_purge_command_removes_old_records(): void
    {
        Carbon::setTestNow(Carbon::parse('2024-01-10 12:00:00'));

        config([
            'filament-threecx.retention.call_logs_days' => 1,
            'filament-threecx.retention.api_audit_days' => 1,
            'filament-threecx.retention.sync_cursor_days' => 1,
        ]);

        $tenant = Tenant::create([
            'name' => 'Tenant',
            'slug' => Str::random(8),
        ]);
        TenantContext::setTenant($tenant);

        $instance = ThreeCxInstance::create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'Instance',
            'base_url' => 'https://threecx.test',
            'verify_tls' => true,
        ]);

        $callLog = ThreeCxCallLog::create([
            'tenant_id' => $tenant->getKey(),
            'instance_id' => $instance->getKey(),
            'direction' => 'inbound',
            'from_number' => '1001',
            'to_number' => '1002',
        ]);
        $callLog->forceFill([
            'created_at' => now()->subDays(5),
            'updated_at' => now()->subDays(5),
        ])->saveQuietly();

        $audit = ThreeCxApiAuditLog::create([
            'tenant_id' => $tenant->getKey(),
            'instance_id' => $instance->getKey(),
            'api_area' => 'xapi',
            'method' => 'GET',
            'path' => '/xapi/health',
        ]);
        $audit->forceFill([
            'created_at' => now()->subDays(5),
            'updated_at' => now()->subDays(5),
        ])->saveQuietly();

        ThreeCxSyncCursor::create([
            'tenant_id' => $tenant->getKey(),
            'instance_id' => $instance->getKey(),
            'entity' => 'contacts',
            'last_synced_at' => now()->subDays(5),
        ]);

        $this->artisan('threecx:purge')->assertExitCode(0);

        $this->assertSame(0, ThreeCxCallLog::query()->count());
        $this->assertSame(0, ThreeCxApiAuditLog::query()->count());
        $this->assertSame(0, ThreeCxSyncCursor::query()->count());

        Carbon::setTestNow();
    }
}
