<?php

namespace Tests\Feature\Blog;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\Blog\Models\BlogPost;
use Haida\Blog\Services\BlogPostService;
use Haida\SiteBuilderCore\Models\Site;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogPostServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_publish_sanitizes_html(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'Tenant Blog',
            'slug' => 'tenant-blog',
            'status' => 'active',
        ]);

        TenantContext::setTenant($tenant);

        $site = Site::query()->create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'Site',
            'slug' => 'site-blog',
            'type' => 'blog',
            'status' => 'published',
        ]);

        $post = BlogPost::query()->create([
            'tenant_id' => $tenant->getKey(),
            'site_id' => $site->getKey(),
            'title' => 'Post',
            'slug' => 'post',
            'status' => 'draft',
            'draft_content' => '<script>alert(1)</script><p>Hello</p>',
        ]);

        app(BlogPostService::class)->publish($post, null);

        $post->refresh();
        $this->assertSame('published', $post->status);
        $this->assertNotNull($post->published_at);
        $this->assertStringNotContainsString('<script>', (string) $post->published_content);
    }
}
