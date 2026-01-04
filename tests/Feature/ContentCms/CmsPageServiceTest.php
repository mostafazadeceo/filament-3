<?php

namespace Tests\Feature\ContentCms;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\ContentCms\Models\CmsPage;
use Haida\ContentCms\Services\CmsPageService;
use Haida\SiteBuilderCore\Models\Site;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class CmsPageServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_publish_sanitizes_html(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'Tenant One',
            'slug' => 'tenant-one',
            'status' => 'active',
        ]);

        TenantContext::setTenant($tenant);

        $site = Site::query()->create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'Site',
            'slug' => 'site',
            'type' => 'website',
            'status' => 'published',
        ]);

        $page = CmsPage::query()->create([
            'tenant_id' => $tenant->getKey(),
            'site_id' => $site->getKey(),
            'slug' => 'home',
            'title' => 'Home',
            'status' => 'draft',
            'draft_content' => [
                'sections' => [
                    [
                        'type' => 'hero',
                        'settings' => [
                            'html' => '<script>alert(1)</script><p>hello</p>',
                        ],
                    ],
                ],
            ],
        ]);

        app(CmsPageService::class)->publish($page, null);

        $page->refresh();
        $this->assertSame('published', $page->status);
        $this->assertNotNull($page->published_at);
        $payload = $page->published_content;
        $this->assertIsArray($payload);
        $html = $payload['sections'][0]['settings']['html'] ?? '';
        $this->assertStringNotContainsString('<script>', $html);
    }

    public function test_publish_requires_sections(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'Tenant Two',
            'slug' => 'tenant-two',
            'status' => 'active',
        ]);

        TenantContext::setTenant($tenant);

        $site = Site::query()->create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'Site',
            'slug' => 'site-2',
            'type' => 'website',
            'status' => 'draft',
        ]);

        $page = CmsPage::query()->create([
            'tenant_id' => $tenant->getKey(),
            'site_id' => $site->getKey(),
            'slug' => 'about',
            'title' => 'About',
            'status' => 'draft',
            'draft_content' => ['invalid' => true],
        ]);

        $this->expectException(InvalidArgumentException::class);

        app(CmsPageService::class)->publish($page, null);
    }
}
