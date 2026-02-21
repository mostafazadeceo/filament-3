<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Http\Controllers\Api\V1;

use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

abstract class ApiController extends Controller
{
    protected function tenantId(): int
    {
        return (int) TenantContext::getTenantId();
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function ok(array $data = [], int $status = 200): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'meta' => [
                'status' => true,
                'message' => 'ok',
            ],
        ], $status);
    }
}
