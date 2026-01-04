<?php

namespace Haida\FilamentStorefrontBuilder\Http\Controllers\Web;

use Haida\FilamentStorefrontBuilder\Models\StorePage;
use Illuminate\Http\JsonResponse;

class PublicPageController
{
    public function show(string $slug): JsonResponse
    {
        $page = StorePage::query()
            ->where('slug', $slug)
            ->where('status', 'published')
            ->where(function ($query) {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->firstOrFail();

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
