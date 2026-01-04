<?php

namespace Haida\FilamentStorefrontBuilder\Services;

use Haida\FilamentStorefrontBuilder\Models\StorePage;
use Haida\FilamentStorefrontBuilder\Models\StorePageVersion;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\DatabaseManager;

class StorefrontPublishService
{
    public function __construct(protected DatabaseManager $db)
    {
    }

    public function publish(StorePage $page, ?Authenticatable $actor = null): StorePage
    {
        return $this->db->transaction(function () use ($page, $actor): StorePage {
            $version = (int) $page->version + 1;

            StorePageVersion::query()->create([
                'tenant_id' => $page->tenant_id,
                'page_id' => $page->getKey(),
                'version' => $version,
                'blocks' => $page->blocks,
                'seo' => $page->seo,
                'status' => $page->status,
                'created_by_user_id' => $actor?->getAuthIdentifier(),
            ]);

            $page->update([
                'status' => 'published',
                'version' => $version,
                'published_at' => now(),
            ]);

            return $page->refresh();
        });
    }
}
