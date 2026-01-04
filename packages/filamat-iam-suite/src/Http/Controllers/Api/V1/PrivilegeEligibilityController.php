<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Http\Controllers\Api\V1;

use Filamat\IamSuite\Http\Controllers\Api\V1\Concerns\ResolvesTenant;
use Filamat\IamSuite\Models\PrivilegeEligibility;
use Filamat\IamSuite\Services\PrivilegeEligibilityService;
use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PrivilegeEligibilityController extends BaseController
{
    use ResolvesTenant;

    protected function modelClass(): string
    {
        return PrivilegeEligibility::class;
    }

    protected function validationRules(string $action): array
    {
        return [
            'user_id' => 'required|integer',
            'role_id' => 'required|integer',
            'reason' => 'required|string',
            'can_request' => 'boolean',
            'active' => 'boolean',
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

        if (! $user || ! $role) {
            return response(['message' => 'کاربر یا نقش یافت نشد.'], 422);
        }
        if (! TenantContext::shouldBypass() && $tenant && ! $this->userBelongsToTenant($user, $tenant)) {
            return response(['message' => 'کاربر در این فضای کاری عضو نیست.'], 422);
        }

        $eligibility = app(PrivilegeEligibilityService::class)->grant(
            $tenant,
            $user,
            $role,
            $request->user(),
            (string) $data['reason']
        );

        $eligibility->update([
            'can_request' => (bool) ($data['can_request'] ?? true),
            'active' => (bool) ($data['active'] ?? true),
        ]);

        return response(['data' => $eligibility], 201);
    }
}
