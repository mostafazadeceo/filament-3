<?php

namespace Haida\FilamentStorefrontBuilder\Http\Controllers\Web;

use Haida\FilamentStorefrontBuilder\Models\StoreTheme;
use Illuminate\Http\JsonResponse;

class PublicThemeController
{
    public function show(): JsonResponse
    {
        $theme = StoreTheme::query()
            ->where('status', 'active')
            ->orderByDesc('activated_at')
            ->first();

        if (! $theme) {
            return response()->json([
                'status' => 'not_found',
                'theme' => null,
            ], 404);
        }

        return response()->json([
            'id' => $theme->getKey(),
            'name' => $theme->name,
            'config' => $theme->config,
            'metadata' => $theme->metadata,
        ]);
    }
}
