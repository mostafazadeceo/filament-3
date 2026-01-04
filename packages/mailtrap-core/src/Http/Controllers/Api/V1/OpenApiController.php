<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Http\Controllers\Api\V1;

use Haida\MailtrapCore\Support\MailtrapOpenApi;

class OpenApiController extends ApiController
{
    public function show(): array
    {
        return MailtrapOpenApi::toArray();
    }
}
