<?php

namespace Haida\Blog\Http\Controllers\Web;

use Filamat\IamSuite\Support\TenantContext;
use Haida\Blog\Models\BlogPost;
use Haida\FeatureGates\Services\FeatureGateService;
use Haida\SiteBuilderCore\Models\Site;
use Haida\TenancyDomains\Support\SiteContext;
use Haida\ThemeEngine\ThemeRegistry;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BlogController extends Controller
{
    public function index()
    {
        [$site, $tenant] = $this->resolveSiteAndTenant();

        if (class_exists(FeatureGateService::class)) {
            $decision = app(FeatureGateService::class)->evaluate($tenant, 'blog.post.view');
            if (! $decision->allowed) {
                throw new NotFoundHttpException('Feature disabled.');
            }
        }

        $posts = BlogPost::query()
            ->where('site_id', $site->getKey())
            ->where('status', 'published')
            ->orderByDesc('published_at')
            ->paginate(10);

        $theme = app(ThemeRegistry::class)->get($site->theme_key ?? 'relograde-v1');

        return view('blog::blog.index', [
            'site' => $site,
            'theme' => $theme,
            'posts' => $posts,
        ]);
    }

    public function show(string $slug)
    {
        [$site, $tenant] = $this->resolveSiteAndTenant();

        if (class_exists(FeatureGateService::class)) {
            $decision = app(FeatureGateService::class)->evaluate($tenant, 'blog.post.view');
            if (! $decision->allowed) {
                throw new NotFoundHttpException('Feature disabled.');
            }
        }

        $post = BlogPost::query()
            ->where('site_id', $site->getKey())
            ->where('slug', $slug)
            ->where('status', 'published')
            ->first();

        if (! $post) {
            throw new NotFoundHttpException('Post not found.');
        }

        $theme = app(ThemeRegistry::class)->get($site->theme_key ?? 'relograde-v1');

        return view('blog::blog.show', [
            'site' => $site,
            'theme' => $theme,
            'post' => $post,
        ]);
    }

    /**
     * @return array{0: Site, 1: \Filamat\IamSuite\Models\Tenant}
     */
    private function resolveSiteAndTenant(): array
    {
        $tenant = TenantContext::getTenant();
        if (! $tenant) {
            throw new NotFoundHttpException('Tenant not found.');
        }

        $siteId = SiteContext::getSiteId();
        $query = Site::query()
            ->where('tenant_id', $tenant->getKey())
            ->where('status', 'published');

        $site = $siteId
            ? $query->where('id', $siteId)->first()
            : $query->orderByDesc('published_at')->first();

        if (! $site) {
            throw new NotFoundHttpException('Site not found.');
        }

        return [$site, $tenant];
    }
}
