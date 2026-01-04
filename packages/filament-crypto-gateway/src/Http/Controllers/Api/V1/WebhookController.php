<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Http\Controllers\Api\V1;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentCryptoGateway\Models\CryptoWebhookCall;
use Haida\FilamentCryptoGateway\Services\WebhookIngestionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebhookController extends ApiController
{
    public function handle(Request $request, string $provider, WebhookIngestionService $service): JsonResponse
    {
        $this->authorize('create', CryptoWebhookCall::class);

        $call = $service->ingest(
            $provider,
            $request->headers->all(),
            (string) $request->getContent(),
            (string) $request->ip(),
            TenantContext::getTenantId()
        );

        return response()->json([
            'status' => $call->status,
            'id' => $call->getKey(),
        ]);
    }
}
