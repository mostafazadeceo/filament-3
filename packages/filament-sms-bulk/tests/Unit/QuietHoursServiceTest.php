<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Tests\Unit;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\SmsBulk\Models\SmsBulkQuietHoursProfile;
use Haida\SmsBulk\Services\QuietHoursService;
use Haida\SmsBulk\Tests\TestCase;
use Illuminate\Support\Carbon;

class QuietHoursServiceTest extends TestCase
{
    public function test_next_allowed_at_is_calculated_when_outside_window(): void
    {
        $tenant = Tenant::create(['name' => 'Tenant', 'slug' => 'tenant-qh']);
        TenantContext::setTenant($tenant);

        $profile = SmsBulkQuietHoursProfile::create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'Office',
            'timezone' => 'Asia/Tehran',
            'allowed_days' => [0, 1, 2, 3, 4, 5, 6],
            'start_time' => '08:00',
            'end_time' => '20:00',
        ]);

        $service = app(QuietHoursService::class);
        $at = Carbon::parse('2026-02-20 02:00:00', 'Asia/Tehran');

        $next = $service->nextAllowedAt($profile, $at);

        $this->assertNotNull($next);
        $this->assertSame('08:00', $next->format('H:i'));
    }
}
