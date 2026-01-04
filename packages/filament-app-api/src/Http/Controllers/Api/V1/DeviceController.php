<?php

declare(strict_types=1);

namespace Haida\FilamentAppApi\Http\Controllers\Api\V1;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentAppApi\Models\AppDevice;
use Haida\FilamentAppApi\Models\AppDeviceToken;
use Illuminate\Http\Request;

class DeviceController
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'device_id' => ['required', 'string', 'max:128'],
            'platform' => ['required', 'string', 'max:32'],
            'name' => ['nullable', 'string', 'max:255'],
            'metadata' => ['nullable', 'array'],
        ]);

        $tenantId = TenantContext::getTenantId();
        if (! $tenantId) {
            return response()->json(['message' => 'فضای کاری مشخص نیست.'], 422);
        }

        $device = AppDevice::query()->updateOrCreate(
            ['tenant_id' => $tenantId, 'device_id' => $data['device_id']],
            [
                'user_id' => $request->user()?->getKey(),
                'platform' => $data['platform'],
                'name' => $data['name'] ?? null,
                'metadata' => $data['metadata'] ?? [],
                'last_seen_at' => now(),
            ]
        );

        return response()->json(['data' => $device]);
    }

    public function storeToken(Request $request, AppDevice $device)
    {
        $data = $request->validate([
            'provider' => ['required', 'string', 'max:32'],
            'token' => ['required', 'string', 'max:512'],
        ]);

        if ($device->tenant_id !== TenantContext::getTenantId()) {
            return response()->json(['message' => 'دستگاه معتبر نیست.'], 403);
        }

        $token = AppDeviceToken::query()->updateOrCreate(
            ['token' => $data['token']],
            [
                'device_id' => $device->getKey(),
                'provider' => $data['provider'],
                'status' => 'active',
                'last_seen_at' => now(),
            ]
        );

        return response()->json(['data' => $token]);
    }

    public function destroy(AppDevice $device)
    {
        if ($device->tenant_id !== TenantContext::getTenantId()) {
            return response()->json(['message' => 'دستگاه معتبر نیست.'], 403);
        }

        $device->delete();

        return response()->json(['message' => 'دستگاه حذف شد.']);
    }
}
