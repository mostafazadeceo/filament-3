<?php

namespace Haida\FilamentStorefrontBuilder\Http\Controllers\Web;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentStorefrontBuilder\Models\StorePage;
use Haida\TenancyDomains\Support\SiteContext;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PublicPageController
{
    public function show(Request $request, string $slug): JsonResponse
    {
        $tenant = TenantContext::getTenant();
        if (! $tenant) {
            throw new NotFoundHttpException('Tenant not found.');
        }

        $siteId = SiteContext::getSiteId();
        $siteIdParam = $request->query('site_id');
        if (is_numeric($siteIdParam)) {
            $siteId = (int) $siteIdParam;
        }

        $query = StorePage::query()
            ->where('slug', $slug)
            ->where('status', 'published')
            ->where(function ($query) {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });

        if ($siteId) {
            $query->where('site_id', $siteId);
        }

        $page = $query->firstOrFail();

        return response()->json([
            'id' => $page->getKey(),
            'title' => $page->title,
            'slug' => $page->slug,
            'blocks' => $page->blocks,
            'seo' => $page->seo,
            'published_at' => $page->published_at,
        ]);
    }
}
