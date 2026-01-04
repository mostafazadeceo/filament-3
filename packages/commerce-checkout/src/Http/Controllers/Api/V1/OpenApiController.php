<?php

namespace Haida\CommerceCheckout\Http\Controllers\Api\V1;

use Haida\CommerceCheckout\Support\CheckoutOpenApi;

class OpenApiController extends ApiController
{
    public function show(): array
    {
        return CheckoutOpenApi::toArray();
    }
}
