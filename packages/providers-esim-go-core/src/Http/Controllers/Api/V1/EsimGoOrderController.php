<?php

declare(strict_types=1);

namespace Haida\ProvidersEsimGoCore\Http\Controllers\Api\V1;

use Filamat\IamSuite\Support\TenantContext;
use Haida\ProvidersEsimGoCore\Models\EsimGoOrder;
use Illuminate\Http\JsonResponse;

class EsimGoOrderController
{
    public function index(): JsonResponse
    {
        $tenantId = TenantContext::getTenantId();
        $orders = EsimGoOrder::query()
            ->where('tenant_id', $tenantId)
            ->orderByDesc('updated_at')
            ->get();

        return response()->json([
            'data' => $orders,
        ]);
    }

    public function show(EsimGoOrder $order): JsonResponse
    {
        return response()->json([
            'data' => $order->loadMissing('esims'),
        ]);
    }
}
