<?php

namespace Haida\FilamentPos\Http\Controllers\Api\V1;

use Haida\FilamentPos\Http\Requests\SyncOutboxRequest;
use Haida\FilamentPos\Models\PosDevice;
use Haida\FilamentPos\Services\PosOutboxService;
use Illuminate\Http\JsonResponse;

class OutboxController extends ApiController
{
    public function upload(SyncOutboxRequest $request, PosOutboxService $service): JsonResponse
    {
        $data = $request->validated();
        $device = null;

        if (! empty($data['device_id'])) {
            $device = PosDevice::query()->find($data['device_id']);
        }

        $result = $service->processEvents($data['events'], $device, auth()->user());

        return response()->json($result);
    }
}
