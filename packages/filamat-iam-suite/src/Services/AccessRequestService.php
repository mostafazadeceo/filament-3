<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services;

use Filamat\IamSuite\Models\AccessRequest;
use Filamat\IamSuite\Models\PermissionOverride;
use Filamat\IamSuite\Models\Tenant;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Role;

class AccessRequestService
{
    public function __construct(
        protected AuditService $auditService,
        protected SecurityEventService $securityEventService
    ) {}

    /**
     * @param  array<int, string>  $permissions
     * @param  array<int, string>  $roles
     * @param  array<string, mixed>  $meta
     */
    public function create(
        Authenticatable $user,
        Tenant $tenant,
        array $permissions = [],
        array $roles = [],
        ?Authenticatable $requestedBy = null,
        ?string $reason = null,
        ?\DateTimeInterface $accessExpiresAt = null,
        ?\DateTimeInterface $requestExpiresAt = null,
        array $meta = []
    ): AccessRequest {
        if (! (bool) config('filamat-iam.features.access_requests', true)) {
            throw new \RuntimeException('درخواست دسترسی غیرفعال است.');
        }

        $request = AccessRequest::query()->create([
            'tenant_id' => $tenant->getKey(),
            'user_id' => $user->getAuthIdentifier(),
            'requested_by_id' => $requestedBy?->getAuthIdentifier(),
            'requested_permissions' => array_values(array_unique($permissions)),
            'requested_roles' => array_values(array_unique($roles)),
            'reason' => $reason,
            'status' => 'pending',
            'access_expires_at' => $accessExpiresAt,
            'request_expires_at' => $requestExpiresAt,
            'meta' => $meta,
        ]);

        $this->auditService->log('access_request.created', $request, [
            'permissions' => $permissions,
            'roles' => $roles,
        ], $requestedBy, $tenant);

        $this->securityEventService->record('access_request.created', 'info', $requestedBy, $tenant, [
            'request_id' => $request->getKey(),
        ]);

        if (! (bool) config('filamat-iam.access_requests.approval_required', true)) {
            return $this->approve($request, $requestedBy);
        }

        return $request;
    }

    public function approve(AccessRequest $request, ?Authenticatable $approver = null, ?string $note = null): AccessRequest
    {
        $request->approvals()->create([
            'approver_id' => $approver?->getAuthIdentifier(),
            'status' => 'approved',
            'decided_at' => now(),
            'note' => $note,
        ]);

        $request->update([
            'status' => 'approved',
            'decided_at' => now(),
            'decided_by_id' => $approver?->getAuthIdentifier(),
            'decision_note' => $note,
        ]);

        $this->applyAccess($request);

        $this->auditService->log('access_request.approved', $request, [], $approver, $request->tenant);
        $this->securityEventService->record('access_request.approved', 'info', $approver, $request->tenant, [
            'request_id' => $request->getKey(),
        ]);

        return $request;
    }

    public function deny(AccessRequest $request, ?Authenticatable $approver = null, ?string $note = null): AccessRequest
    {
        $request->approvals()->create([
            'approver_id' => $approver?->getAuthIdentifier(),
            'status' => 'denied',
            'decided_at' => now(),
            'note' => $note,
        ]);

        $request->update([
            'status' => 'denied',
            'decided_at' => now(),
            'decided_by_id' => $approver?->getAuthIdentifier(),
            'decision_note' => $note,
        ]);

        $this->auditService->log('access_request.denied', $request, [], $approver, $request->tenant);
        $this->securityEventService->record('access_request.denied', 'warning', $approver, $request->tenant, [
            'request_id' => $request->getKey(),
        ]);

        return $request;
    }

    public function expirePending(): int
    {
        return AccessRequest::query()
            ->where('status', 'pending')
            ->whereNotNull('request_expires_at')
            ->where('request_expires_at', '<', now())
            ->update(['status' => 'expired', 'decided_at' => now()]);
    }

    protected function applyAccess(AccessRequest $request): void
    {
        $expiresAt = $request->access_expires_at;
        $permissions = Arr::wrap($request->requested_permissions ?? []);
        foreach ($permissions as $permissionKey) {
            PermissionOverride::query()->updateOrCreate([
                'tenant_id' => $request->tenant_id,
                'user_id' => $request->user_id,
                'permission_key' => $permissionKey,
            ], [
                'effect' => 'allow',
                'expires_at' => $expiresAt,
            ]);
        }

        if (! (bool) config('filamat-iam.access_requests.apply_roles_as_permissions', true)) {
            return;
        }

        $roles = Arr::wrap($request->requested_roles ?? []);
        foreach ($roles as $roleName) {
            $role = Role::query()
                ->where('tenant_id', $request->tenant_id)
                ->where('name', $roleName)
                ->first();
            if (! $role) {
                continue;
            }

            foreach ($role->permissions as $permission) {
                PermissionOverride::query()->updateOrCreate([
                    'tenant_id' => $request->tenant_id,
                    'user_id' => $request->user_id,
                    'permission_key' => $permission->name,
                ], [
                    'effect' => 'allow',
                    'expires_at' => $expiresAt,
                ]);
            }
        }
    }
}
