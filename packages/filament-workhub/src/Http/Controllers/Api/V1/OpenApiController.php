<?php

namespace Haida\FilamentWorkhub\Http\Controllers\Api\V1;

use Haida\FilamentWorkhub\Support\WorkhubOpenApi;
use Illuminate\Http\JsonResponse;

class OpenApiController extends ApiController
{
    public function show(): JsonResponse
    {
        return response()->json(WorkhubOpenApi::toArray());
    }
}
