<?php

namespace Haida\FilamentCommerceExperience\Http\Controllers\Api\V1;

use Haida\FilamentCommerceExperience\Support\ExperienceOpenApi;
use Illuminate\Http\JsonResponse;

class OpenApiController
{
    public function show(): JsonResponse
    {
        return response()->json(ExperienceOpenApi::toArray());
    }
}
