<?php

namespace Haida\FilamentCommerceCore\Http\Controllers\Api\V1;

use Haida\FilamentCommerceCore\Support\CommerceCoreOpenApi;

class OpenApiController extends ApiController
{
    public function show(): array
    {
        return CommerceCoreOpenApi::toArray();
    }
}
