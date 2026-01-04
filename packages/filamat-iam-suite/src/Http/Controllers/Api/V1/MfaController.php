<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Http\Controllers\Api\V1;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Services\MfaService;
use Filamat\IamSuite\Services\ProtectedActionService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MfaController
{
    public function startTotp(Request $request): Response
    {
        $user = $request->user();
        if (! $user) {
            return response(['message' => 'کاربر احراز نشده است.'], 401);
        }

        $tenant = $this->resolveTenant($request);
        $result = app(MfaService::class)->beginTotpEnrollment($user, $tenant);

        return response([
            'data' => [
                'secret' => $result['secret'],
                'method_id' => $result['method']->getKey(),
            ],
        ], 200);
    }

    public function confirmTotp(Request $request): Response
    {
        $data = $request->validate([
            'code' => 'required|string',
        ]);

        $user = $request->user();
        if (! $user) {
            return response(['message' => 'کاربر احراز نشده است.'], 401);
        }

        $tenant = $this->resolveTenant($request);
        $result = app(MfaService::class)->confirmTotpEnrollment($user, (string) $data['code'], $tenant);

        return response([
            'data' => [
                'backup_codes' => $result['backup_codes'],
            ],
        ], 200);
    }

    public function resetTotp(Request $request): Response
    {
        $data = $request->validate([
            'reason' => 'required|string',
            'password' => 'nullable|string',
            'totp' => 'nullable|string',
            'backup_code' => 'nullable|string',
        ]);

        $user = $request->user();
        if (! $user) {
            return response(['message' => 'کاربر احراز نشده است.'], 401);
        }

        $tenant = $this->resolveTenant($request);

        $requiresStepUp = in_array('iam.mfa.reset', (array) config('filamat-iam.protected_actions.require_mfa_actions', []), true);
        if ($requiresStepUp) {
            $protected = app(ProtectedActionService::class);
            if (! empty($data['totp'])) {
                $token = $protected->issueWithTotp($user, 'iam.mfa.reset', $data['totp'], $tenant);
                $protected->requireToken($user, 'iam.mfa.reset', $tenant, $token);
            } elseif (! empty($data['backup_code'])) {
                $token = $protected->issueWithBackupCode($user, 'iam.mfa.reset', $data['backup_code'], $tenant);
                $protected->requireToken($user, 'iam.mfa.reset', $tenant, $token);
            } elseif (! empty($data['password'])) {
                $token = $protected->issueWithPassword($user, 'iam.mfa.reset', $data['password'], $tenant);
                $protected->requireToken($user, 'iam.mfa.reset', $tenant, $token);
            } else {
                return response(['message' => 'تایید هویت مجدد لازم است.'], 403);
            }
        }

        app(MfaService::class)->reset($user, $tenant, $request->user(), (string) $data['reason']);

        return response(['message' => 'MFA ریست شد.'], 200);
    }

    protected function resolveTenant(Request $request): ?Tenant
    {
        $tenantId = $request->header(config('filamat-iam.api.tenant_header', 'X-Tenant-ID'));
        if (! $tenantId) {
            return null;
        }

        return Tenant::query()->find($tenantId);
    }
}
