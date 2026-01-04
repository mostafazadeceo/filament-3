<?php

namespace Haida\FilamentStorefrontBuilder\Http\Controllers\Web;

use Haida\FilamentStorefrontBuilder\Models\StoreBlock;
use Illuminate\Http\JsonResponse;

class PublicBlockController
{
    public function show(string $key): JsonResponse
    {
        $block = StoreBlock::query()
            ->where('key', $key)
            ->where('status', 'active')
            ->firstOrFail();

        return response()->json([
            'key' => $block->key,
            'type' => $block->type,
            'name' => $block->name,
            'schema' => $block->schema,
            'content' => $block->content,
        ]);
    }
}
