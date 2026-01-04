<?php

namespace Haida\FilamentStorefrontBuilder\Http\Controllers\Web;

use Haida\FilamentStorefrontBuilder\Models\StoreMenu;
use Haida\FilamentStorefrontBuilder\Models\StoreMenuItem;
use Illuminate\Http\JsonResponse;

class PublicMenuController
{
    public function show(string $key): JsonResponse
    {
        $menu = StoreMenu::query()
            ->where('key', $key)
            ->where('status', 'active')
            ->firstOrFail();

        $items = StoreMenuItem::query()
            ->where('menu_id', $menu->getKey())
            ->orderBy('sort_order')
            ->get();

        $tree = $this->buildTree($items);

        return response()->json([
            'key' => $menu->key,
            'name' => $menu->name,
            'items' => $tree,
        ]);
    }

    /**
     * @param  \Illuminate\Support\Collection<int, StoreMenuItem>  $items
     * @return array<int, array<string, mixed>>
     */
    protected function buildTree($items): array
    {
        $grouped = $items->groupBy('parent_id');

        $build = function ($parentId) use (&$build, $grouped): array {
            return ($grouped[$parentId] ?? collect())->map(function (StoreMenuItem $item) use (&$build): array {
                return [
                    'id' => $item->getKey(),
                    'label' => $item->label,
                    'url' => $item->url,
                    'page_id' => $item->page_id,
                    'children' => $build($item->getKey()),
                ];
            })->all();
        };

        return $build(null);
    }
}
