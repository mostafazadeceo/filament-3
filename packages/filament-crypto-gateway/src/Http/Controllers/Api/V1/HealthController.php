<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Http\Controllers\Api\V1;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentCryptoGateway\Models\CryptoProviderAccount;
use Haida\FilamentCryptoGateway\Services\HealthService;
use Illuminate\Http\JsonResponse;

class HealthController extends ApiController
{
    public function providers(HealthService $service): JsonResponse
    {
        $this->authorize('viewAny', CryptoProviderAccount::class);

        return response()->json([
            'providers' => $service->providers(TenantContext::getTenantId()),
        ]);
    }

    public function nodes(): JsonResponse
    {
        if (class_exists(\Haida\FilamentCryptoNodes\Models\CryptoNodeConnector::class)) {
            $this->authorize('viewAny', \Haida\FilamentCryptoNodes\Models\CryptoNodeConnector::class);
        }

        $payload = [];

        if (class_exists(\Haida\FilamentCryptoNodes\Services\NodeHealthService::class)) {
            $payload = app(\Haida\FilamentCryptoNodes\Services\NodeHealthService::class)->nodes(TenantContext::getTenantId());
        }

        return response()->json([
            'nodes' => $payload,
        ]);
    }
}
