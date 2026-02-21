<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Tests\Unit;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\SmsBulk\Services\SuppressionService;
use Haida\SmsBulk\Tests\TestCase;

class SuppressionServiceTest extends TestCase
{
    public function test_opt_out_adds_recipient_to_blocked_filters(): void
    {
        $tenant = Tenant::create(['name' => 'Tenant', 'slug' => 'tenant-supp']);
        TenantContext::setTenant($tenant);

        $service = app(SuppressionService::class);
        $service->applyOptOut($tenant->getKey(), '09120000001', 'keyword');

        $filtered = $service->filterRecipients($tenant->getKey(), ['09120000001', '09120000002']);

        $this->assertSame(['09120000002'], $filtered['allowed']);
        $this->assertContains('09120000001', $filtered['blocked']);
    }
}
