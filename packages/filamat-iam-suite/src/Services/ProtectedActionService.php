<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services;

use Filamat\IamSuite\Models\ProtectedActionToken;
use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ProtectedActionService
{
    public const SESSION_KEY = 'filamat_iam_protected_action_tokens';

    public function __construct(
        protected SecurityEventService $securityEventService,
        protected MfaService $mfaService
    ) {}

    public function issueWithPassword(
        Authenticatable $user,
        string $action,
        string $password,
        ?Tenant $tenant = null,
        bool $storeInSession = false
    ): string {
        if (! Hash::check($password, $user->getAuthPassword())) {
            $this->securityEventService->record('protected_action.password_failed', 'warning', $user, $tenant, [
                'action' => $action,
            ]);

            throw new \RuntimeException('احراز هویت مجدد ناموفق بود.');
        }

        return $this->issueToken($user, $action, $tenant, 'password', $storeInSession);
    }

    public function issueWithTotp(
        Authenticatable $user,
        string $action,
        string $code,
        ?Tenant $tenant = null,
        bool $storeInSession = false
    ): string {
        if (! $this->mfaService->verifyTotp($user, $code, $tenant)) {
            $this->securityEventService->record('protected_action.mfa_failed', 'warning', $user, $tenant, [
                'action' => $action,
            ]);

            throw new \RuntimeException('کد MFA معتبر نیست.');
        }

        return $this->issueToken($user, $action, $tenant, 'totp', $storeInSession);
    }

    public function issueWithBackupCode(
        Authenticatable $user,
        string $action,
        string $code,
        ?Tenant $tenant = null,
        bool $storeInSession = false
    ): string {
        if (! $this->mfaService->verifyBackupCode($user, $code, $tenant)) {
            $this->securityEventService->record('protected_action.backup_failed', 'warning', $user, $tenant, [
                'action' => $action,
            ]);

            throw new \RuntimeException('کد پشتیبان معتبر نیست.');
        }

        return $this->issueToken($user, $action, $tenant, 'backup_code', $storeInSession);
    }

    public function issueToken(
        Authenticatable $user,
        string $action,
        ?Tenant $tenant = null,
        string $verifiedVia = 'password',
        bool $storeInSession = false
    ): string {
        if (! $this->isEnabled()) {
            return '';
        }

        $tenant ??= TenantContext::getTenant();
        $token = Str::random(64);
        $ttl = (int) config('filamat-iam.protected_actions.ttl_minutes', 10);

        $record = ProtectedActionToken::query()->create([
            'tenant_id' => $tenant?->getKey(),
            'user_id' => $user->getAuthIdentifier(),
            'action' => $action,
            'token_hash' => hash('sha256', $token),
            'verified_via' => $verifiedVia,
            'issued_at' => now(),
            'expires_at' => now()->addMinutes($ttl),
            'ip' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ]);

        if ($storeInSession && function_exists('session')) {
            $payload = session()->get(self::SESSION_KEY, []);
            $payload[$action] = [
                'token' => $token,
                'id' => $record->getKey(),
                'expires_at' => $record->expires_at?->toISOString(),
            ];
            session()->put(self::SESSION_KEY, $payload);
        }

        return $token;
    }

    public function requireToken(
        Authenticatable $user,
        string $action,
        ?Tenant $tenant = null,
        ?string $token = null
    ): void {
        if (! $this->isEnabled()) {
            return;
        }

        $tenant ??= TenantContext::getTenant();

        $token ??= $this->getSessionToken($action);
        if (! $token) {
            throw new \RuntimeException('تایید هویت مجدد لازم است.');
        }

        $record = $this->findValidToken($user, $action, $tenant, $token);
        if (! $record) {
            throw new \RuntimeException('توکن تایید نامعتبر است.');
        }

        $record->update(['used_at' => now()]);
    }

    public function isEnabled(): bool
    {
        return (bool) config('filamat-iam.features.protected_actions', true)
            && (bool) config('filamat-iam.protected_actions.enabled', true);
    }

    protected function getSessionToken(string $action): ?string
    {
        if (! function_exists('session')) {
            return null;
        }

        $payload = session()->get(self::SESSION_KEY, []);
        if (! is_array($payload)) {
            return null;
        }

        return $payload[$action]['token'] ?? null;
    }

    protected function findValidToken(
        Authenticatable $user,
        string $action,
        ?Tenant $tenant,
        string $token
    ): ?ProtectedActionToken {
        $query = ProtectedActionToken::query()
            ->where('user_id', $user->getAuthIdentifier())
            ->where('action', $action)
            ->whereNull('used_at')
            ->where(function ($builder) {
                $builder->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->orderByDesc('id');

        if ($tenant) {
            $query->where('tenant_id', $tenant->getKey());
        }

        $record = $query->first();
        if (! $record) {
            return null;
        }

        if (! hash_equals($record->token_hash, hash('sha256', $token))) {
            return null;
        }

        return $record;
    }
}
