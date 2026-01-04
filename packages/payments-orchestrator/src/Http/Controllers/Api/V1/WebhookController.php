<?php

namespace Haida\PaymentsOrchestrator\Http\Controllers\Api\V1;

use Haida\PaymentsOrchestrator\Services\WebhookHandler;
use Illuminate\Http\Request;

class WebhookController
{
    public function __construct(protected WebhookHandler $handler)
    {
    }

    public function __invoke(Request $request, string $provider): array
    {
        return $this->handler->handle($request, $provider);
    }
}
