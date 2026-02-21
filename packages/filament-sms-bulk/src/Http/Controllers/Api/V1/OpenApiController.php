<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Http\Controllers\Api\V1;

use Haida\SmsBulk\Support\SmsBulkOpenApi;

class OpenApiController extends ApiController
{
    public function __invoke()
    {
        return response()->json(SmsBulkOpenApi::build());
    }
}
