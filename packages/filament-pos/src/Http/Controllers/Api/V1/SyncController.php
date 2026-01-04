<?php

namespace Haida\FilamentPos\Http\Controllers\Api\V1;

use Haida\FilamentPos\Models\PosDevice;
use Haida\FilamentPos\Services\PosSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SyncController extends ApiController
{
    public function snapshot(Request $request, PosSyncService $service): JsonResponse
    {
        $device = $this->resolveDevice($request->input('device_id'));

        return response()->json($service->snapshot($device));
    }

    public function delta(Request $request, PosSyncService $service): JsonResponse
    {
        $cursor = (string) $request->query('cursor', '');
        if (! $cursor) {
            return response()->json($service->snapshot($this->resolveDevice($request->input('device_id'))));
        }

        $device = $this->resolveDevice($request->input('device_id'));

        return response()->json($service->delta($cursor, $device));
    }

    protected function resolveDevice(?int $deviceId): ?PosDevice
    {
        if (! $deviceId) {
            return null;
        }

        return PosDevice::query()->find($deviceId);
    }
}
