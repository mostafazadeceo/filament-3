<?php

namespace Haida\FilamentMarketplaceConnectors\Http\Controllers\Api\V1;

use Haida\FilamentMarketplaceConnectors\Support\MarketplaceOpenApi;
use Illuminate\Http\JsonResponse;

class OpenApiController
{
    public function show(): JsonResponse
    {
        return response()->json(MarketplaceOpenApi::toArray());
    }
}
