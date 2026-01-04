<?php

namespace Haida\FilamentPos\Http\Controllers\Api\V1;

use Haida\FilamentPos\Support\PosOpenApi;
use Illuminate\Http\JsonResponse;

class OpenApiController extends ApiController
{
    public function show(): JsonResponse
    {
        return response()->json(PosOpenApi::toArray());
    }
}
