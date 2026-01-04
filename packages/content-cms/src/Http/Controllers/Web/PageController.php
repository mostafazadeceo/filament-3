<?php

namespace Haida\ContentCms\Http\Controllers\Web;

use Filamat\IamSuite\Support\TenantContext;
use Haida\ContentCms\Models\CmsPage;
use Haida\SiteBuilderCore\Models\Site;
use Haida\TenancyDomains\Support\SiteContext;
use Haida\ThemeEngine\ThemeRegistry;
use Haida\FeatureGates\Services\FeatureGateService;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PageController extends Controller
{
    public function home()
    {
        $homeSlug = config('content-cms.public.home_slug', 'home');

        return $this->show($homeSlug);
    }

    public function show(?string $slug = null)
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

        $slug = $slug ?: config('content-cms.public.home_slug', 'home');

        $page = CmsPage::query()
            ->where('site_id', $site->getKey())
            ->where('slug', $slug)
            ->where('status', 'published')
            ->first();

        if (! $page) {
            throw new NotFoundHttpException('Page not found.');
        }

        $theme = app(ThemeRegistry::class)->get($site->theme_key ?? 'relograde-v1');

        return view('content-cms::pages.show', [
            'page' => $page,
            'site' => $site,
            'theme' => $theme,
        ]);
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
