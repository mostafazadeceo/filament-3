<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Http\Controllers\Api\V1;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentCryptoGateway\Http\Requests\RunReconcileRequest;
use Haida\FilamentCryptoGateway\Models\CryptoReconciliation;
use Haida\FilamentCryptoGateway\Services\ReconcileService;
use Illuminate\Http\JsonResponse;

class ReconcileController extends ApiController
{
    public function run(RunReconcileRequest $request, ReconcileService $service): JsonResponse
    {
        $this->authorize('create', CryptoReconciliation::class);

        $scope = (string) ($request->validated()['scope'] ?? 'invoices');
        $tenantId = (int) TenantContext::getTenantId();

        $record = $service->run($tenantId, $scope);

        return response()->json([
            'id' => $record->getKey(),
            'status' => $record->status,
            'result' => $record->result_json,
        ]);
    }
}
