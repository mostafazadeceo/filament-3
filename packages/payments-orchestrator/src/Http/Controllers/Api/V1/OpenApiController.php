<?php

namespace Haida\PaymentsOrchestrator\Http\Controllers\Api\V1;

use Haida\PaymentsOrchestrator\Support\PaymentsOpenApi;

class OpenApiController extends ApiController
{
    public function show(): array
    {
        return PaymentsOpenApi::toArray();
    }
}
