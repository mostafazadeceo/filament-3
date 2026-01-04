<?php

namespace Haida\FilamentPayments\Http\Controllers\Api\V1;

use Haida\FilamentPayments\Support\PaymentsOpenApi;
use Illuminate\Http\JsonResponse;

class OpenApiController extends ApiController
{
    public function show(): JsonResponse
    {
        return response()->json(PaymentsOpenApi::toArray());
    }
}
