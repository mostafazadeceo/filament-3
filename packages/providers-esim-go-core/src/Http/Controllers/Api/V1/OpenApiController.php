<?php

declare(strict_types=1);

namespace Haida\ProvidersEsimGoCore\Http\Controllers\Api\V1;

use Haida\ProvidersEsimGoCore\Support\EsimGoOpenApi;
use Illuminate\Http\JsonResponse;

class OpenApiController
{
    public function show(): JsonResponse
    {
        return response()->json(EsimGoOpenApi::toArray());
    }
}
