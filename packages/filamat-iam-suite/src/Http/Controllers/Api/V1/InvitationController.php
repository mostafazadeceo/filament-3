<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Http\Controllers\Api\V1;

use Filamat\IamSuite\Http\Controllers\Api\V1\Concerns\ResolvesTenant;
use Filamat\IamSuite\Models\UserInvitation;
use Filamat\IamSuite\Services\InviteUserService;
use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InvitationController extends BaseController
{
    use ResolvesTenant;

    protected function modelClass(): string
    {
        return UserInvitation::class;
    }

    protected function validationRules(string $action): array
    {
        return [
            'email' => 'required|email',
            'name' => 'nullable|string',
            'roles' => 'array',
            'roles.*' => 'string',
            'permissions' => 'array',
            'permissions.*' => 'string',
            'reason' => 'required|string',
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

        $result = app(InviteUserService::class)->invite(
            $tenant,
            (string) $data['email'],
            (string) ($data['name'] ?? ''),
            array_values((array) ($data['roles'] ?? [])),
            array_values((array) ($data['permissions'] ?? [])),
            $request->user(),
            (string) $data['reason'],
            isset($data['expires_at']) ? new \DateTime($data['expires_at']) : null
        );

        return response(['data' => $result['invitation']], 201);
    }

    public function accept(UserInvitation $invitation, Request $request): Response
    {
        $tenant = $this->resolveTenant($request);
        if ($response = $this->ensureTenantRequest($request, $tenant)) {
            return $response;
        }
        if ($tenant && ! TenantContext::shouldBypass() && (int) $invitation->tenant_id !== (int) $tenant->getKey()) {
            return response(['message' => 'دعوت‌نامه معتبر نیست.'], 404);
        }

        $data = $request->validate([
            'token' => 'required|string',
        ]);

        app(InviteUserService::class)->accept($invitation, (string) $data['token'], $request->user());

        return response(['data' => $invitation->fresh()], 200);
    }

    public function revoke(UserInvitation $invitation, Request $request): Response
    {
        $tenant = $this->resolveTenant($request);
        if ($response = $this->ensureTenantRequest($request, $tenant)) {
            return $response;
        }
        if ($tenant && ! TenantContext::shouldBypass() && (int) $invitation->tenant_id !== (int) $tenant->getKey()) {
            return response(['message' => 'دعوت‌نامه معتبر نیست.'], 404);
        }

        $data = $request->validate([
            'reason' => 'required|string',
        ]);

        app(InviteUserService::class)->revoke($invitation, $request->user(), (string) $data['reason']);

        return response(['data' => $invitation->fresh()], 200);
    }
}
