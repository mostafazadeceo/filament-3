<?php

declare(strict_types=1);

namespace Haida\FilamentAppApi\Http\Controllers\Api\V1;

use Haida\FilamentAppApi\Support\AppOpenApi;

class OpenApiController
{
    public function show()
    {
        return response()->json(AppOpenApi::toArray());
    }
}
