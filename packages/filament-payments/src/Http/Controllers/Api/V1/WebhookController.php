<?php

namespace Haida\FilamentPayments\Http\Controllers\Api\V1;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentPayments\Models\PaymentWebhookEvent;
use Haida\FilamentPayments\Services\WebhookHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebhookController extends ApiController
{
    public function handle(Request $request, string $provider, WebhookHandler $handler): JsonResponse
    {
        $this->authorize('create', PaymentWebhookEvent::class);

        $event = $handler->handle(
            $provider,
            $request->headers->all(),
            $request->all(),
            TenantContext::getTenantId(),
            $request->getContent()
        );

        return response()->json([
            'status' => $event->status,
            'signature_valid' => $event->signature_valid,
            'event_id' => $event->getKey(),
        ]);
    }
}
