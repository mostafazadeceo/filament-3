<?php

namespace Tests\Feature\PageBuilder;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\PageBuilder\Models\PageTemplate;
use Haida\PageBuilder\Services\PageBuilderService;
use Haida\SiteBuilderCore\Models\Site;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class PageBuilderServiceTest extends TestCase
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
            'name' => 'Main Site',
            'slug' => 'main-site',
            'type' => 'website',
            'status' => 'draft',
        ]);

        $template = PageTemplate::query()->create([
            'tenant_id' => $tenant->getKey(),
            'site_id' => $site->getKey(),
            'name' => 'Landing',
            'key' => 'landing',
            'draft_content' => [
                'sections' => [
                    [
                        'type' => 'hero',
                        'settings' => [
                            'content' => '<p>سلام</p><script>alert(1)</script>',
                        ],
                    ],
                ],
            ],
        ]);

        $service = app(PageBuilderService::class);
        $service->publish($template, null);

        $template->refresh();

        $published = $template->published_content;
        $content = $published['sections'][0]['settings']['content'] ?? '';

        $this->assertStringNotContainsString('<script>', $content);
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
            'name' => 'Site Two',
            'slug' => 'site-two',
            'type' => 'website',
            'status' => 'draft',
        ]);

        $template = PageTemplate::query()->create([
            'tenant_id' => $tenant->getKey(),
            'site_id' => $site->getKey(),
            'name' => 'Empty',
            'key' => 'empty',
            'draft_content' => [
                'invalid' => true,
            ],
        ]);

        $service = app(PageBuilderService::class);

        $this->expectException(InvalidArgumentException::class);

        $service->publish($template, null);
    }
}
