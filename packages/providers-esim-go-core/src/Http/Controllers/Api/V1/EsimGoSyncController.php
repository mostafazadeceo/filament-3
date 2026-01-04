<?php

declare(strict_types=1);

namespace Haida\ProvidersEsimGoCore\Http\Controllers\Api\V1;

use Filamat\IamSuite\Support\TenantContext;
use Haida\ProvidersCore\DataTransferObjects\ProviderContext;
use Haida\ProvidersCore\Services\ProviderJobDispatcher;
use Haida\ProvidersCore\Support\ProviderAction;
use Haida\ProvidersEsimGoCore\Models\EsimGoConnection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EsimGoSyncController
{
    public function store(Request $request, ProviderJobDispatcher $dispatcher): JsonResponse
    {
        $type = (string) $request->input('type', 'catalogue');
        $connectionId = $request->input('connection_id');
        $tenantId = TenantContext::getTenantId();

        $connection = $connectionId
            ? EsimGoConnection::query()->where('tenant_id', $tenantId)->find($connectionId)
            : EsimGoConnection::query()->where('tenant_id', $tenantId)->default()->first();

        if (! $connection) {
            return response()->json(['message' => 'اتصال پیدا نشد.'], 404);
        }

        $action = match ($type) {
            'inventory' => ProviderAction::SyncInventory,
            default => ProviderAction::SyncProducts,
        };

        $payload = (array) $request->input('payload', []);

        $context = new ProviderContext($connection->tenant_id, $connection->getKey(), (bool) ($connection->metadata['sandbox'] ?? false));
        $log = $dispatcher->dispatch($action, $context, 'esim-go', $payload);

        return response()->json([
            'status' => 'queued',
            'job_log_id' => $log->getKey(),
        ]);
    }
}
