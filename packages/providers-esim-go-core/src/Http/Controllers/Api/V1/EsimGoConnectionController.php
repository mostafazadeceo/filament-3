<?php

declare(strict_types=1);

namespace Haida\ProvidersEsimGoCore\Http\Controllers\Api\V1;

use Filamat\IamSuite\Support\TenantContext;
use Haida\ProvidersEsimGoCore\Models\EsimGoConnection;
use Illuminate\Http\JsonResponse;

class EsimGoConnectionController
{
    public function index(): JsonResponse
    {
        $tenantId = TenantContext::getTenantId();
        $connections = EsimGoConnection::query()
            ->where('tenant_id', $tenantId)
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'data' => $connections,
        ]);
    }
}
