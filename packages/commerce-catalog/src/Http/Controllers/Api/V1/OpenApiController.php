<?php

namespace Haida\CommerceCatalog\Http\Controllers\Api\V1;

use Haida\CommerceCatalog\Support\CatalogOpenApi;

class OpenApiController extends ApiController
{
    public function show(): array
    {
        return CatalogOpenApi::toArray();
    }
}
