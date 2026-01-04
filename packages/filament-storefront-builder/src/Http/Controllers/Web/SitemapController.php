<?php

namespace Haida\FilamentStorefrontBuilder\Http\Controllers\Web;

use Haida\FilamentStorefrontBuilder\Models\StorePage;
use Illuminate\Http\Response;

class SitemapController
{
    public function __invoke(): Response
    {
        $pages = StorePage::query()
            ->where('status', 'published')
            ->where(function ($query) {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->get();

        $urls = $pages->map(function (StorePage $page) {
            return [
                'loc' => url($page->slug === 'home' ? '/' : '/'.$page->slug),
                'lastmod' => $page->updated_at?->toAtomString(),
            ];
        });

        $items = $urls->map(function (array $url): string {
            $loc = htmlspecialchars($url['loc'], ENT_XML1);
            $lastmod = $url['lastmod'] ? '<lastmod>'.$url['lastmod'].'</lastmod>' : '';

            return '<url><loc>'.$loc.'</loc>'.$lastmod.'</url>';
        })->implode('');

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'
            .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'
            .$items
            .'</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
