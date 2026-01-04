<?php

declare(strict_types=1);

namespace Haida\FilamentAppApi\Http\Controllers\Api\V1;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentAppApi\Services\SyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SyncController
{
    public function __construct(private readonly SyncService $syncService) {}

    public function push(Request $request)
    {
        if (! TenantContext::getTenantId()) {
            return response()->json(['message' => 'فضای کاری مشخص نیست.'], 422);
        }

        $data = $request->validate([
            'items' => ['required', 'array'],
            'items.*.id' => ['required', 'string'],
            'items.*.module' => ['required', 'string'],
            'items.*.action' => ['required', 'string'],
            'items.*.payload' => ['nullable', 'array'],
            'items.*.idempotency_key' => ['nullable', 'string'],
        ]);

        $results = $this->syncService->push($data['items']);
        $accepted = collect($results)->where('status', 'accepted')->count();
        $failed = collect($results)->where('status', 'failed')->count();
        Log::info('app.sync.push', [
            'tenant_id' => TenantContext::getTenantId(),
            'items' => count($data['items']),
            'accepted' => $accepted,
            'failed' => $failed,
        ]);

        return response()->json(['results' => $results]);
    }

    public function pull(Request $request)
    {
        if (! TenantContext::getTenantId()) {
            return response()->json(['message' => 'فضای کاری مشخص نیست.'], 422);
        }

        $cursor = $request->query('cursor');
        $response = $this->syncService->pull($cursor ? (string) $cursor : null);
        Log::info('app.sync.pull', [
            'tenant_id' => TenantContext::getTenantId(),
            'changes' => count($response['changes'] ?? []),
        ]);

        return response()->json($response);
    }

    public function conflicts(Request $request)
    {
        if (! TenantContext::getTenantId()) {
            return response()->json(['message' => 'فضای کاری مشخص نیست.'], 422);
        }

        $request->validate([
            'items' => ['required', 'array'],
        ]);

        return response()->json(['status' => 'ok']);
    }
}
