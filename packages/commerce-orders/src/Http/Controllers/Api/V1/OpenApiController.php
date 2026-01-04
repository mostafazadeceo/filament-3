<?php

namespace Haida\CommerceOrders\Http\Controllers\Api\V1;

use Haida\CommerceOrders\Support\OrdersOpenApi;

class OpenApiController extends ApiController
{
    public function show(): array
    {
        return OrdersOpenApi::toArray();
    }
}
