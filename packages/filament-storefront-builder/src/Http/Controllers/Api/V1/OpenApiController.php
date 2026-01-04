<?php

namespace Haida\FilamentStorefrontBuilder\Http\Controllers\Api\V1;

use Haida\FilamentStorefrontBuilder\Support\StorefrontOpenApi;
use Illuminate\Http\JsonResponse;

class OpenApiController
{
    public function show(): JsonResponse
    {
        return response()->json(StorefrontOpenApi::toArray());
    }
}
