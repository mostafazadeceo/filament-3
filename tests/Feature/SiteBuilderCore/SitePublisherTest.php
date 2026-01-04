<?php

namespace Tests\Feature\SiteBuilderCore;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\SiteBuilderCore\Enums\SiteStatus;
use Haida\SiteBuilderCore\Models\Site;
use Haida\SiteBuilderCore\Services\SitePublisher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitePublisherTest extends TestCase
{
    use RefreshDatabase;

    public function test_publish_updates_status_and_history(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'Tenant One',
            'slug' => 'tenant-one',
            'status' => 'active',
        ]);

        TenantContext::setTenant($tenant);

        $site = Site::query()->create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'Main Site',
            'slug' => 'main-site',
            'type' => 'website',
            'status' => SiteStatus::Draft->value,
        ]);

        $publisher = app(SitePublisher::class);
        $publisher->publish($site, null);

        $site->refresh();

        $this->assertSame(SiteStatus::Published->value, $site->status);
        $this->assertNotNull($site->published_at);

        $this->assertDatabaseHas($this->historyTable(), [
            'tenant_id' => $tenant->getKey(),
            'site_id' => $site->getKey(),
            'to_status' => SiteStatus::Published->value,
        ]);
    }

    public function test_tenant_scope_limits_sites(): void
    {
        $tenantA = Tenant::query()->create([
            'name' => 'Tenant A',
            'slug' => 'tenant-a',
            'status' => 'active',
        ]);

        $tenantB = Tenant::query()->create([
            'name' => 'Tenant B',
            'slug' => 'tenant-b',
            'status' => 'active',
        ]);

        Site::query()->create([
            'tenant_id' => $tenantA->getKey(),
            'name' => 'Site A',
            'slug' => 'site-a',
            'type' => 'website',
            'status' => SiteStatus::Draft->value,
        ]);

        Site::query()->create([
            'tenant_id' => $tenantB->getKey(),
            'name' => 'Site B',
            'slug' => 'site-b',
            'type' => 'blog',
            'status' => SiteStatus::Draft->value,
        ]);

        TenantContext::setTenant($tenantA);

        $this->assertSame(1, Site::query()->count());
        $this->assertTrue(Site::query()->where('slug', 'site-a')->exists());
        $this->assertFalse(Site::query()->where('slug', 'site-b')->exists());
    }

    private function historyTable(): string
    {
        return config('site-builder-core.tables.site_publish_histories', 'site_publish_histories');
    }
}
