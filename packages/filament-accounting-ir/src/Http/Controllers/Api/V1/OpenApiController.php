<?php

namespace Vendor\FilamentAccountingIr\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Vendor\FilamentAccountingIr\Support\AccountingOpenApi;

class OpenApiController extends ApiController
{
    public function show(): JsonResponse
    {
        return response()->json(AccountingOpenApi::toArray());
    }
}
