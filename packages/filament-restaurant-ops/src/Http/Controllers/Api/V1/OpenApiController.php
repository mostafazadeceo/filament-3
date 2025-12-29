<?php

namespace Haida\FilamentRestaurantOps\Http\Controllers\Api\V1;

use Haida\FilamentRestaurantOps\Support\RestaurantOpsOpenApi;
use Illuminate\Http\JsonResponse;

class OpenApiController extends ApiController
{
    public function show(): JsonResponse
    {
        return response()->json(RestaurantOpsOpenApi::toArray());
    }
}
