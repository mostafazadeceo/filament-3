<?php

declare(strict_types=1);

namespace Haida\FilamentMailOps\Http\Controllers\Api\V1;

use Haida\FilamentMailOps\Support\MailOpsOpenApi;

class OpenApiController extends ApiController
{
    public function show(): \Illuminate\Http\JsonResponse
    {
        return response()->json(MailOpsOpenApi::toArray());
    }
}
