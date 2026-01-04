<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Http\Controllers\Api\V1;

use Filamat\IamSuite\Http\Controllers\Api\V1\Concerns\ResolvesTenant;
use Filamat\IamSuite\Services\ImpersonationService;
use Filamat\IamSuite\Services\ProtectedActionService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ImpersonationApiController
{
    use ResolvesTenant;

    public function start(Request $request): Response
    {
        $data = $request->validate([
            'user_id' => 'required|integer',
            'tenant_id' => 'nullable|integer',
            'reason' => 'required|string',
            'ticket_id' => 'required|string',
            'ttl_minutes' => 'nullable|integer|min:5',
            'restricted' => 'boolean',
            'password' => 'nullable|string',
            'totp' => 'nullable|string',
            'backup_code' => 'nullable|string',
        ]);

        $tenant = $this->resolveTenant($request);
        if ($response = $this->ensureTenantRequest($request, $tenant)) {
            return $response;
        }

        $userModel = config('auth.providers.users.model');
        $target = $userModel::query()->find($data['user_id']);

        if (! $target || ! $tenant) {
            return response(['message' => 'کاربر یا فضای کاری یافت نشد.'], 422);
        }

        $actor = $request->user();
        if (! $actor) {
            return response(['message' => 'کاربر احراز نشده است.'], 401);
        }

        $requiresStepUp = in_array('iam.impersonate', (array) config('filamat-iam.protected_actions.require_mfa_actions', []), true);
        if ($requiresStepUp) {
            $protected = app(ProtectedActionService::class);
            if (! empty($data['totp'])) {
                $token = $protected->issueWithTotp($actor, 'iam.impersonate', $data['totp'], $tenant);
                $protected->requireToken($actor, 'iam.impersonate', $tenant, $token);
            } elseif (! empty($data['backup_code'])) {
                $token = $protected->issueWithBackupCode($actor, 'iam.impersonate', $data['backup_code'], $tenant);
                $protected->requireToken($actor, 'iam.impersonate', $tenant, $token);
            } elseif (! empty($data['password'])) {
                $token = $protected->issueWithPassword($actor, 'iam.impersonate', $data['password'], $tenant);
                $protected->requireToken($actor, 'iam.impersonate', $tenant, $token);
            } else {
                return response(['message' => 'تایید هویت مجدد لازم است.'], 403);
            }
        }

        $session = app(ImpersonationService::class)->start(
            $actor,
            $target,
            $tenant,
            (string) $data['reason'],
            (string) $data['ticket_id'],
            isset($data['ttl_minutes']) ? (int) $data['ttl_minutes'] : null,
            (bool) ($data['restricted'] ?? true)
        );

        return response(['data' => $session], 201);
    }

    public function stop(Request $request): Response
    {
        $data = $request->validate([
            'reason' => 'nullable|string',
        ]);

        app(ImpersonationService::class)->stop($data['reason'] ?? null, $request->user());

        return response(['message' => 'امپرسونیشن متوقف شد.'], 200);
    }
}
