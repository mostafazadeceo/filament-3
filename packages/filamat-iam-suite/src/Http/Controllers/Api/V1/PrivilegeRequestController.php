<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Http\Controllers\Api\V1;

use Filamat\IamSuite\Http\Controllers\Api\V1\Concerns\ResolvesTenant;
use Filamat\IamSuite\Models\PrivilegeRequest;
use Filamat\IamSuite\Services\PrivilegeElevationService;
use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PrivilegeRequestController extends BaseController
{
    use ResolvesTenant;

    protected function modelClass(): string
    {
        return PrivilegeRequest::class;
    }

    protected function validationRules(string $action): array
    {
        return [
            'user_id' => 'required|integer',
            'role_id' => 'required|integer',
            'ticket_id' => 'required|string',
            'reason' => 'required|string',
            'requested_duration_minutes' => 'required|integer|min:5',
            'request_expires_at' => 'nullable|date',
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

        $requestModel = app(PrivilegeElevationService::class)->request(
            $tenant,
            $user,
            $role,
            (int) $data['requested_duration_minutes'],
            $request->user(),
            (string) $data['reason'],
            (string) $data['ticket_id'],
            isset($data['request_expires_at']) ? new \DateTime($data['request_expires_at']) : null
        );

        return response(['data' => $requestModel], 201);
    }

    public function approve(PrivilegeRequest $requestModel, Request $request): Response
    {
        $data = $request->validate([
            'note' => 'required|string',
        ]);

        app(PrivilegeElevationService::class)->approve($requestModel, $request->user(), (string) $data['note']);

        return response(['data' => $requestModel->fresh()], 200);
    }

    public function deny(PrivilegeRequest $requestModel, Request $request): Response
    {
        $data = $request->validate([
            'note' => 'required|string',
        ]);

        app(PrivilegeElevationService::class)->deny($requestModel, $request->user(), (string) $data['note']);

        return response(['data' => $requestModel->fresh()], 200);
    }
}
