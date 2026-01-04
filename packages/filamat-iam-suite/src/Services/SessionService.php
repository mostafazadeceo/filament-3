<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Models\UserSession;
use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SessionService
{
    public function __construct(
        protected AuditService $auditService,
        protected SecurityEventService $securityEventService
    ) {}

    public function recordLogin(Authenticatable $user, ?Tenant $tenant = null): ?UserSession
    {
        if (! (bool) config('filamat-iam.sessions.record', true)) {
            return null;
        }

        if (! function_exists('session')) {
            return null;
        }

        if (! Schema::hasTable((new UserSession)->getTable())) {
            return null;
        }

        $sessionId = session()->getId();
        if (! $sessionId) {
            return null;
        }

        $tenant ??= TenantContext::getTenant();

        return UserSession::query()->updateOrCreate([
            'session_id' => $sessionId,
        ], [
            'tenant_id' => $tenant?->getKey(),
            'user_id' => $user->getAuthIdentifier(),
            'ip' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'last_activity_at' => now(),
            'revoked_at' => null,
            'revoked_by_id' => null,
            'revoke_reason' => null,
        ]);
    }

    public function recordLogout(Authenticatable $user, ?Tenant $tenant = null): void
    {
        if (! function_exists('session')) {
            return;
        }

        if (! Schema::hasTable((new UserSession)->getTable())) {
            return;
        }

        $sessionId = session()->getId();
        if (! $sessionId) {
            return;
        }

        $tenant ??= TenantContext::getTenant();

        UserSession::query()
            ->where('session_id', $sessionId)
            ->update([
                'revoked_at' => now(),
                'revoke_reason' => 'logout',
            ]);

        $this->auditService->log('session.logout', $user, [], $user, $tenant);
        $this->securityEventService->record('session.logout', 'info', $user, $tenant, [
            'session_id' => $sessionId,
        ]);
    }

    public function touch(string $sessionId): void
    {
        if (! (bool) config('filamat-iam.sessions.record', true)) {
            return;
        }

        if (! Schema::hasTable((new UserSession)->getTable())) {
            return;
        }

        UserSession::query()
            ->where('session_id', $sessionId)
            ->update(['last_activity_at' => now()]);
    }

    public function revoke(UserSession $session, ?Authenticatable $actor = null, ?string $reason = null): UserSession
    {
        $session->update([
            'revoked_at' => now(),
            'revoked_by_id' => $actor?->getAuthIdentifier(),
            'revoke_reason' => $reason,
        ]);

        $this->deleteSessionRecord($session->session_id);

        $this->auditService->log('session.revoked', $session, ['reason' => $reason], $actor, $session->tenant);
        $this->securityEventService->record('session.revoked', 'warning', $actor, $session->tenant, [
            'session_id' => $session->session_id,
            'user_id' => $session->user_id,
        ]);

        return $session;
    }

    public function cleanup(?int $days = null): int
    {
        $days ??= (int) config('filamat-iam.sessions.retention_days', 30);
        if ($days <= 0) {
            return 0;
        }

        if (! Schema::hasTable((new UserSession)->getTable())) {
            return 0;
        }

        return UserSession::query()
            ->where('created_at', '<', now()->subDays($days))
            ->delete();
    }

    protected function deleteSessionRecord(?string $sessionId): void
    {
        if (! $sessionId) {
            return;
        }

        if (config('session.driver') === 'database') {
            DB::table('sessions')->where('id', $sessionId)->delete();
        }
    }
}
