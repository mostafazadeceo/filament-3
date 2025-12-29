<?php

namespace Haida\FilamentPettyCashIr\Http\Controllers\Api\V1;

use Haida\FilamentPettyCashIr\Support\PettyCashOpenApi;
use Illuminate\Http\JsonResponse;

class OpenApiController extends ApiController
{
    public function show(): JsonResponse
    {
        return response()->json(PettyCashOpenApi::toArray());
    }
}
