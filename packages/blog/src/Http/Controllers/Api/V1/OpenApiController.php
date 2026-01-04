<?php

namespace Haida\Blog\Http\Controllers\Api\V1;

use Haida\Blog\Support\BlogOpenApi;

class OpenApiController extends ApiController
{
    public function show(): array
    {
        return BlogOpenApi::toArray();
    }
}
