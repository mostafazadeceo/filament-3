<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services;

use Filamat\IamSuite\Models\ImpersonationSession;
use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\IamAuthorization;
use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use RuntimeException;

class ImpersonationService
{
    public const SESSION_IMPERSONATOR = 'filamat_iam_impersonator_id';

    public const SESSION_IMPERSONATED = 'filamat_iam_impersonated_id';

    public const SESSION_IMPERSONATED_TENANT = 'filamat_iam_impersonated_tenant_id';

    public const SESSION_IMPERSONATED_AT = 'filamat_iam_impersonated_at';

    public const SESSION_IMPERSONATION_TOKEN = 'filamat_iam_impersonation_token';

    public const SESSION_IMPERSONATION_ID = 'filamat_iam_impersonation_id';

    public const SESSION_IMPERSONATION_CAN_WRITE = 'filamat_iam_impersonation_can_write';

    public function __construct(
        protected AuditService $auditService,
        protected SecurityEventService $securityEventService
    ) {}

    public function start(
        Authenticatable $actor,
        Authenticatable $target,
        ?Tenant $tenant = null,
        ?string $reason = null,
        ?string $ticketId = null,
        ?int $ttlMinutes = null,
        ?bool $restricted = null
    ): ImpersonationSession {
        if (! (bool) config('filamat-iam.impersonation.enabled', true)) {
            throw new RuntimeException('امپرسونیشن غیرفعال است.');
        }

        if (config('filamat-iam.impersonation.require_reason', true) && ! $reason) {
            throw new RuntimeException('دلیل امپرسونیشن الزامی است.');
        }

        if (config('filamat-iam.impersonation.require_ticket', true) && ! $ticketId) {
            throw new RuntimeException('شناسه تیکت الزامی است.');
        }

        if (! IamAuthorization::allows('iam.impersonate', $tenant, $actor)) {
            throw new RuntimeException('اجازه امپرسونیشن ندارید.');
        }

        if ($tenant && ! IamAuthorization::allows('iam.impersonate.cross_tenant', $tenant, $actor)) {
            $currentTenantId = TenantContext::getTenantId();
            if ($currentTenantId && $currentTenantId !== $tenant->getKey()) {
                throw new RuntimeException('اجازه امپرسونیشن بین فضای کاری ندارید.');
            }
        }

        if ($tenant && method_exists($target, 'tenants')) {
            $belongs = $target->tenants()->where('tenants.id', $tenant->getKey())->exists();
            if (! $belongs) {
                throw new RuntimeException('کاربر در این فضای کاری عضو نیست.');
            }
        }

        $restricted ??= (bool) config('filamat-iam.impersonation.restricted_default', true);
        $canWrite = ! $restricted && IamAuthorization::allows('iam.impersonate.write', $tenant, $actor);

        if (! $canWrite) {
            $restricted = true;
        }

        $maxMinutes = (int) config('filamat-iam.impersonation.max_minutes', 120);
        $ttlMinutes = $ttlMinutes ? min($ttlMinutes, $maxMinutes) : $maxMinutes;

        $token = Str::random(64);

        $session = ImpersonationSession::query()->create([
            'tenant_id' => $tenant?->getKey(),
            'impersonator_id' => $actor->getAuthIdentifier(),
            'impersonated_id' => $target->getAuthIdentifier(),
            'token_hash' => hash('sha256', $token),
            'ticket_id' => $ticketId,
            'reason' => $reason,
            'restricted' => $restricted,
            'can_write' => $canWrite,
            'ip' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'started_at' => now(),
            'expires_at' => now()->addMinutes($ttlMinutes),
        ]);

        Auth::login($target);

        if (function_exists('session')) {
            session()->regenerate();
            session()->put(self::SESSION_IMPERSONATOR, $actor->getAuthIdentifier());
            session()->put(self::SESSION_IMPERSONATED, $target->getAuthIdentifier());
            session()->put(self::SESSION_IMPERSONATED_TENANT, $tenant?->getKey());
            session()->put(self::SESSION_IMPERSONATED_AT, now()->toISOString());
            session()->put(self::SESSION_IMPERSONATION_TOKEN, $token);
            session()->put(self::SESSION_IMPERSONATION_ID, $session->getKey());
            session()->put(self::SESSION_IMPERSONATION_CAN_WRITE, $canWrite);
        }

        $session->update(['session_id' => function_exists('session') ? session()->getId() : null]);

        TenantContext::setTenant($tenant);

        $this->auditService->log('impersonation.start', $session, [
            'actor_id' => $actor->getAuthIdentifier(),
            'tenant_id' => $tenant?->getKey(),
            'restricted' => $restricted,
        ], $actor, $tenant);

        $this->securityEventService->record('impersonation.start', 'warning', $actor, $tenant, [
            'target_id' => $target->getAuthIdentifier(),
            'session_id' => $session->getKey(),
        ]);

        return $session;
    }

    public function stop(?string $reason = null, ?Authenticatable $endedBy = null): void
    {
        $impersonatorId = session()->get(self::SESSION_IMPERSONATOR);
        $impersonatedId = session()->get(self::SESSION_IMPERSONATED);
        $tenantId = session()->get(self::SESSION_IMPERSONATED_TENANT);
        $sessionId = session()->get(self::SESSION_IMPERSONATION_ID);

        if (! $impersonatorId) {
            return;
        }

        $userModel = config('auth.providers.users.model');
        $impersonator = $userModel::query()->find($impersonatorId);
        $impersonated = $impersonatedId ? $userModel::query()->find($impersonatedId) : null;
        $tenant = $tenantId ? Tenant::query()->find($tenantId) : null;

        $session = $sessionId ? ImpersonationSession::query()->find($sessionId) : null;
        if ($session) {
            $session->update([
                'ended_at' => now(),
                'ended_by_id' => $endedBy?->getAuthIdentifier() ?? $impersonator?->getKey(),
                'end_reason' => $reason,
            ]);
        }

        session()->forget([
            self::SESSION_IMPERSONATOR,
            self::SESSION_IMPERSONATED,
            self::SESSION_IMPERSONATED_TENANT,
            self::SESSION_IMPERSONATED_AT,
            self::SESSION_IMPERSONATION_TOKEN,
            self::SESSION_IMPERSONATION_ID,
            self::SESSION_IMPERSONATION_CAN_WRITE,
        ]);

        if ($impersonator) {
            Auth::login($impersonator);
            if (function_exists('session')) {
                session()->regenerate();
            }
        }

        TenantContext::setTenant(null);

        $this->auditService->log('impersonation.stop', $session ?? $impersonated, [
            'actor_id' => $impersonator?->getKey(),
            'tenant_id' => $tenant?->getKey(),
            'reason' => $reason,
        ], $impersonator, $tenant);

        $this->securityEventService->record('impersonation.stop', 'info', $impersonator, $tenant, [
            'target_id' => $impersonated?->getKey(),
            'session_id' => $session?->getKey(),
        ]);
    }

    public function isImpersonating(): bool
    {
        if (! function_exists('session') || ! session()->has(self::SESSION_IMPERSONATOR)) {
            return false;
        }

        $session = $this->validatedSession();
        if (! $session) {
            $this->stop('invalid_token');

            return false;
        }

        return true;
    }

    public function currentSession(): ?ImpersonationSession
    {
        return $this->validatedSession();
    }

    public function canWrite(): bool
    {
        if (! function_exists('session')) {
            return false;
        }

        return (bool) session()->get(self::SESSION_IMPERSONATION_CAN_WRITE, false);
    }

    public function isExpired(): bool
    {
        $session = $this->currentSession();
        if (! $session || ! $session->expires_at) {
            return false;
        }

        return $session->expires_at->isPast();
    }

    protected function validatedSession(): ?ImpersonationSession
    {
        if (! function_exists('session')) {
            return null;
        }

        $sessionId = session()->get(self::SESSION_IMPERSONATION_ID);
        $token = session()->get(self::SESSION_IMPERSONATION_TOKEN);

        if (! $sessionId || ! $token) {
            return null;
        }

        $session = ImpersonationSession::query()->find($sessionId);
        if (! $session) {
            return null;
        }

        if (! hash_equals($session->token_hash, hash('sha256', (string) $token))) {
            return null;
        }

        return $session;
    }
}
