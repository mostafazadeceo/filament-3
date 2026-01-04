<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services;

use Filamat\IamSuite\Models\PrivilegeActivation;
use Filamat\IamSuite\Models\PrivilegeRequest;
use Filamat\IamSuite\Models\Tenant;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PrivilegeElevationService
{
    public function __construct(
        protected AuditService $auditService,
        protected SecurityEventService $securityEventService,
        protected ProtectedActionService $protectedActionService,
        protected PrivilegeEligibilityService $eligibilityService
    ) {}

    public function request(
        Tenant $tenant,
        Authenticatable $user,
        Role $role,
        int $durationMinutes,
        ?Authenticatable $requestedBy = null,
        ?string $reason = null,
        ?string $ticketId = null,
        ?\DateTimeInterface $requestExpiresAt = null
    ): PrivilegeRequest {
        $this->ensurePamEnabled();

        if (! $this->eligibilityService->isEligible($tenant, $user, $role)) {
            throw new \RuntimeException('کاربر واجد شرایط این نقش نیست.');
        }

        $maxMinutes = (int) config('filamat-iam.pam.max_minutes', 240);
        $durationMinutes = min($durationMinutes, $maxMinutes);

        $requiresMfa = in_array($role->name, (array) config('filamat-iam.pam.require_mfa_roles', []), true);

        $request = PrivilegeRequest::query()->create([
            'tenant_id' => $tenant->getKey(),
            'user_id' => $user->getAuthIdentifier(),
            'role_id' => $role->getKey(),
            'requested_by_id' => $requestedBy?->getAuthIdentifier(),
            'ticket_id' => $ticketId,
            'reason' => $reason,
            'status' => 'pending',
            'requested_duration_minutes' => $durationMinutes,
            'request_expires_at' => $requestExpiresAt,
            'requires_mfa' => $requiresMfa,
        ]);

        $this->auditService->log('pam.requested', $request, [
            'role' => $role->name,
            'duration' => $durationMinutes,
        ], $requestedBy, $tenant);
        $this->securityEventService->record('pam.requested', 'info', $requestedBy, $tenant, [
            'request_id' => $request->getKey(),
        ]);

        if (! (bool) config('filamat-iam.pam.approval_required', true)) {
            return $this->approve($request, $requestedBy, 'auto');
        }

        return $request;
    }

    public function approve(PrivilegeRequest $request, ?Authenticatable $approver = null, ?string $note = null): PrivilegeRequest
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

        $this->auditService->log('pam.approved', $request, [], $approver, $request->tenant);
        $this->securityEventService->record('pam.approved', 'info', $approver, $request->tenant, [
            'request_id' => $request->getKey(),
        ]);

        return $request;
    }

    public function deny(PrivilegeRequest $request, ?Authenticatable $approver = null, ?string $note = null): PrivilegeRequest
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

        $this->auditService->log('pam.denied', $request, [], $approver, $request->tenant);
        $this->securityEventService->record('pam.denied', 'warning', $approver, $request->tenant, [
            'request_id' => $request->getKey(),
        ]);

        return $request;
    }

    public function activate(
        Tenant $tenant,
        Authenticatable $user,
        Role $role,
        ?PrivilegeRequest $request = null,
        ?Authenticatable $actor = null,
        ?string $reason = null,
        ?string $ticketId = null,
        ?\DateTimeInterface $expiresAt = null
    ): PrivilegeActivation {
        $this->ensurePamEnabled();

        if ($request && $request->status !== 'approved') {
            throw new \RuntimeException('درخواست تایید نشده است.');
        }

        if ($request && $request->requires_mfa) {
            $this->protectedActionService->requireToken($actor ?? $user, 'iam.pam.activate', $tenant);
        }

        $maxMinutes = (int) config('filamat-iam.pam.max_minutes', 240);
        $expiresAt ??= now()->addMinutes(min($request?->requested_duration_minutes ?? $maxMinutes, $maxMinutes));

        app(PermissionRegistrar::class)->setPermissionsTeamId($tenant->getKey());
        if (method_exists($user, 'assignRole')) {
            $user->assignRole($role);
        }

        $activation = PrivilegeActivation::query()->create([
            'tenant_id' => $tenant->getKey(),
            'user_id' => $user->getAuthIdentifier(),
            'role_id' => $role->getKey(),
            'request_id' => $request?->getKey(),
            'activated_by_id' => $actor?->getAuthIdentifier(),
            'ticket_id' => $ticketId ?? $request?->ticket_id,
            'reason' => $reason ?? $request?->reason,
            'status' => 'active',
            'activated_at' => now(),
            'expires_at' => $expiresAt,
        ]);

        if (function_exists('session')) {
            session()->regenerate();
        }

        $this->auditService->log('pam.activated', $activation, [], $actor, $tenant);
        $this->securityEventService->record('pam.activated', 'warning', $actor, $tenant, [
            'activation_id' => $activation->getKey(),
        ]);

        return $activation;
    }

    public function revoke(PrivilegeActivation $activation, ?Authenticatable $actor = null, ?string $reason = null): PrivilegeActivation
    {
        $activation->update([
            'status' => 'revoked',
            'revoked_at' => now(),
            'revoked_by_id' => $actor?->getAuthIdentifier(),
            'revoke_reason' => $reason,
        ]);

        $user = $activation->user;
        if ($user && method_exists($user, 'removeRole') && $activation->role) {
            app(PermissionRegistrar::class)->setPermissionsTeamId($activation->tenant_id);
            $user->removeRole($activation->role);
        }

        if (function_exists('session')) {
            session()->regenerate();
        }

        $this->auditService->log('pam.revoked', $activation, ['reason' => $reason], $actor, $activation->tenant);
        $this->securityEventService->record('pam.revoked', 'warning', $actor, $activation->tenant, [
            'activation_id' => $activation->getKey(),
        ]);

        return $activation;
    }

    public function expirePendingRequests(): int
    {
        if (! Schema::hasTable((new PrivilegeRequest)->getTable())) {
            return 0;
        }

        return PrivilegeRequest::query()
            ->where('status', 'pending')
            ->whereNotNull('request_expires_at')
            ->where('request_expires_at', '<', now())
            ->update([
                'status' => 'expired',
                'decided_at' => now(),
            ]);
    }

    public function expireDueActivations(?Tenant $tenant = null): int
    {
        if (! Schema::hasTable((new PrivilegeActivation)->getTable())) {
            return 0;
        }

        $query = PrivilegeActivation::query()
            ->where('status', 'active')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now());

        if ($tenant) {
            $query->where('tenant_id', $tenant->getKey());
        }

        $count = 0;
        $query->chunkById(50, function ($activations) use (&$count) {
            foreach ($activations as $activation) {
                $activation->update([
                    'status' => 'expired',
                    'revoked_at' => now(),
                    'revoke_reason' => 'expired',
                ]);

                $user = $activation->user;
                if ($user && method_exists($user, 'removeRole') && $activation->role) {
                    app(PermissionRegistrar::class)->setPermissionsTeamId($activation->tenant_id);
                    $user->removeRole($activation->role);
                }

                $this->auditService->log('pam.expired', $activation, [], null, $activation->tenant);
                $this->securityEventService->record('pam.expired', 'info', null, $activation->tenant, [
                    'activation_id' => $activation->getKey(),
                ]);
                $count++;
            }
        });

        return $count;
    }

    public function autoExpireIfNeeded(): void
    {
        if (! (bool) config('filamat-iam.pam.auto_expire_on_boot', true)) {
            return;
        }

        $cooldown = (int) config('filamat-iam.pam.auto_expire_cooldown_seconds', 300);
        $cacheKey = 'filamat-iam.pam.auto-expire';
        if (! Cache::add($cacheKey, true, now()->addSeconds($cooldown))) {
            return;
        }

        $this->expireDueActivations();
        $this->expirePendingRequests();
    }

    protected function ensurePamEnabled(): void
    {
        if (! (bool) config('filamat-iam.features.pam', true)) {
            throw new \RuntimeException('دسترسی ممتاز غیرفعال است.');
        }
    }
}
