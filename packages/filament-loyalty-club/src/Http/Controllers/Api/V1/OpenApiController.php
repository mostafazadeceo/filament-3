<?php

namespace Haida\FilamentLoyaltyClub\Http\Controllers\Api\V1;

use Haida\FilamentLoyaltyClub\Support\LoyaltyOpenApi;
use Illuminate\Http\JsonResponse;

class OpenApiController extends ApiController
{
    public function show(): JsonResponse
    {
        return response()->json(LoyaltyOpenApi::toArray());
    }
}
