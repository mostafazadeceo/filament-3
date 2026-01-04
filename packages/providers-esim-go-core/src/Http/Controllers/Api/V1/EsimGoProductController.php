<?php

declare(strict_types=1);

namespace Haida\ProvidersEsimGoCore\Http\Controllers\Api\V1;

use Filamat\IamSuite\Support\TenantContext;
use Haida\ProvidersEsimGoCore\Models\EsimGoProduct;
use Illuminate\Http\JsonResponse;

class EsimGoProductController
{
    public function index(): JsonResponse
    {
        $tenantId = TenantContext::getTenantId();
        $products = EsimGoProduct::query()
            ->where('tenant_id', $tenantId)
            ->orderByDesc('updated_at')
            ->get();

        return response()->json([
            'data' => $products,
        ]);
    }

    public function show(EsimGoProduct $product): JsonResponse
    {
        return response()->json([
            'data' => $product,
        ]);
    }
}
