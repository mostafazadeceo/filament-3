<?php

namespace Haida\ContentCms\Http\Controllers\Api\V1;

use Haida\ContentCms\Support\ContentCmsOpenApi;

class OpenApiController extends ApiController
{
    public function show(): array
    {
        return ContentCmsOpenApi::toArray();
    }
}
