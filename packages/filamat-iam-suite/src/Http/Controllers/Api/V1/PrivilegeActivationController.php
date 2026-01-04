<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Http\Controllers\Api\V1;

use Filamat\IamSuite\Http\Controllers\Api\V1\Concerns\ResolvesTenant;
use Filamat\IamSuite\Models\PrivilegeActivation;
use Filamat\IamSuite\Models\PrivilegeRequest;
use Filamat\IamSuite\Services\PrivilegeElevationService;
use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PrivilegeActivationController extends BaseController
{
    use ResolvesTenant;

    protected function modelClass(): string
    {
        return PrivilegeActivation::class;
    }

    protected function validationRules(string $action): array
    {
        return [
            'user_id' => 'required|integer',
            'role_id' => 'required|integer',
            'request_id' => 'nullable|integer',
            'reason' => 'required|string',
            'ticket_id' => 'nullable|string',
            'expires_at' => 'nullable|date',
        ];
    }

    public function store(Request $request, ?int $parentId = null): Response
    {
        $data = $request->validate($this->validationRules('store'));

        $tenant = $this->resolveTenant($request);
        if ($response = $this->ensureTenantRequest($request, $tenant)) {
            return $response;
        }

        $userModel = config('auth.providers.users.model');
        $user = $userModel::query()->find($data['user_id']);
        $role = $tenant ? $this->findTenantRole((int) $data['role_id'], $tenant) : null;
        $privilegeRequest = ! empty($data['request_id']) ? PrivilegeRequest::query()->find($data['request_id']) : null;

        if (! $user || ! $role) {
            return response(['message' => 'کاربر یا نقش یافت نشد.'], 422);
        }
        if (! TenantContext::shouldBypass() && $tenant && ! $this->userBelongsToTenant($user, $tenant)) {
            return response(['message' => 'کاربر در این فضای کاری عضو نیست.'], 422);
        }
        if ($privilegeRequest && $tenant && ! TenantContext::shouldBypass() && (int) $privilegeRequest->tenant_id !== (int) $tenant->getKey()) {
            return response(['message' => 'درخواست معتبر نیست.'], 422);
        }
        if ($privilegeRequest && ((int) $privilegeRequest->user_id !== (int) $user->getAuthIdentifier() || (int) $privilegeRequest->role_id !== (int) $role->getKey())) {
            return response(['message' => 'درخواست با کاربر یا نقش همخوانی ندارد.'], 422);
        }

        $activation = app(PrivilegeElevationService::class)->activate(
            $tenant,
            $user,
            $role,
            $privilegeRequest,
            $request->user(),
            (string) $data['reason'],
            $data['ticket_id'] ?? null,
            isset($data['expires_at']) ? new \DateTime($data['expires_at']) : null
        );

        return response(['data' => $activation], 201);
    }

    public function revoke(PrivilegeActivation $activation, Request $request): Response
    {
        $data = $request->validate([
            'reason' => 'required|string',
        ]);

        app(PrivilegeElevationService::class)->revoke($activation, $request->user(), (string) $data['reason']);

        return response(['data' => $activation->fresh()], 200);
    }
}
