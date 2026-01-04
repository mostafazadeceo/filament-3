<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Http\Controllers\Api\V1;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Services\ProtectedActionService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProtectedActionController
{
    public function verify(Request $request): Response
    {
        $data = $request->validate([
            'action' => 'required|string',
            'password' => 'nullable|string',
            'totp' => 'nullable|string',
            'backup_code' => 'nullable|string',
        ]);

        $user = $request->user();
        if (! $user) {
            return response(['message' => 'کاربر احراز نشده است.'], 401);
        }

        $tenant = null;
        $tenantId = $request->header(config('filamat-iam.api.tenant_header', 'X-Tenant-ID'));
        if ($tenantId) {
            $tenant = Tenant::query()->find($tenantId);
        }

        $service = app(ProtectedActionService::class);
        $action = (string) $data['action'];

        if (! empty($data['totp'])) {
            $token = $service->issueWithTotp($user, $action, (string) $data['totp'], $tenant);
        } elseif (! empty($data['backup_code'])) {
            $token = $service->issueWithBackupCode($user, $action, (string) $data['backup_code'], $tenant);
        } elseif (! empty($data['password'])) {
            $token = $service->issueWithPassword($user, $action, (string) $data['password'], $tenant);
        } else {
            return response(['message' => 'اطلاعات تایید نامعتبر است.'], 422);
        }

        return response([
            'data' => [
                'token' => $token,
                'expires_at' => now()->addMinutes((int) config('filamat-iam.protected_actions.ttl_minutes', 10))->toISOString(),
            ],
        ], 200);
    }
}
