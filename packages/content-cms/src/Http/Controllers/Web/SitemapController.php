<?php

namespace Haida\ContentCms\Http\Controllers\Web;

use Filamat\IamSuite\Support\TenantContext;
use Haida\ContentCms\Models\CmsPage;
use Haida\FeatureGates\Services\FeatureGateService;
use Haida\SiteBuilderCore\Models\Site;
use Haida\TenancyDomains\Support\SiteContext;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SitemapController extends Controller
{
    public function __invoke()
    {
        $site = $this->resolveSite();
        if (! $site) {
            throw new NotFoundHttpException('Site not found.');
        }

        $tenant = TenantContext::getTenant();
        if (! $tenant) {
            throw new NotFoundHttpException('Tenant not found.');
        }

        if (class_exists(FeatureGateService::class)) {
            $decision = app(FeatureGateService::class)->evaluate($tenant, 'cms.page.view');
            if (! $decision->allowed) {
                throw new NotFoundHttpException('Feature disabled.');
            }
        }

        $baseUrl = request()->getSchemeAndHttpHost();

        $pages = CmsPage::query()
            ->where('site_id', $site->getKey())
            ->where('status', 'published')
            ->get(['slug', 'updated_at']);

        $urls = [];
        foreach ($pages as $page) {
            $path = $page->slug === config('content-cms.public.home_slug', 'home') ? '/' : '/'.$page->slug;
            $urls[] = [
                'loc' => $baseUrl.$path,
                'lastmod' => optional($page->updated_at)->toAtomString(),
            ];
        }

        if (class_exists(\Haida\Blog\Models\BlogPost::class)) {
            $posts = \Haida\Blog\Models\BlogPost::query()
                ->where('site_id', $site->getKey())
                ->where('status', 'published')
                ->get(['slug', 'updated_at']);

            foreach ($posts as $post) {
                $urls[] = [
                    'loc' => $baseUrl.'/blog/'.$post->slug,
                    'lastmod' => optional($post->updated_at)->toAtomString(),
                ];
            }
        }

        $xml = view('content-cms::pages.sitemap', [
            'urls' => $urls,
        ])->render();

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }

    private function resolveSite(): ?Site
    {
        $tenant = TenantContext::getTenant();
        if (! $tenant) {
            return null;
        }

        $siteId = SiteContext::getSiteId();
        $query = Site::query()
            ->where('tenant_id', $tenant->getKey())
            ->where('status', 'published');

        if ($siteId) {
            return $query->where('id', $siteId)->first();
        }

        return $query->orderByDesc('published_at')->first();
    }
}
