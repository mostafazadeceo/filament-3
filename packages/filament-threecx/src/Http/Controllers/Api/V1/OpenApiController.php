<?php

namespace Haida\FilamentThreeCx\Http\Controllers\Api\V1;

use Haida\FilamentThreeCx\Support\ThreeCxOpenApi;

class OpenApiController extends ApiController
{
    public function show(): array
    {
        return ThreeCxOpenApi::toArray();
    }
}
