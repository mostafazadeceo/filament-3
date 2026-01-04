<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Http\Controllers\Api\V1;

use Haida\FilamentCryptoGateway\Support\CryptoOpenApi;
use Illuminate\Http\JsonResponse;

class OpenApiController extends ApiController
{
    public function show(): JsonResponse
    {
        return response()->json(CryptoOpenApi::toArray());
    }
}
