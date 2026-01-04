<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services;

use Filamat\IamSuite\Models\MfaMethod;
use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

class MfaService
{
    public function __construct(
        protected SecurityEventService $securityEventService
    ) {}

    /**
     * @return array{method: MfaMethod, secret: string}
     */
    public function beginTotpEnrollment(Authenticatable $user, ?Tenant $tenant = null): array
    {
        $this->ensureTotpEnabled();

        $tenant ??= TenantContext::getTenant();
        $secret = app(Google2FA::class)->generateSecretKey();

        $method = MfaMethod::query()->updateOrCreate([
            'user_id' => $user->getAuthIdentifier(),
            'tenant_id' => $tenant?->getKey(),
            'type' => 'totp',
        ], [
            'secret' => $secret,
            'enabled_at' => null,
            'revoked_at' => null,
        ]);

        $this->securityEventService->record('mfa.totp.enroll_started', 'info', $user, $tenant, [
            'method_id' => $method->getKey(),
        ]);

        return [
            'method' => $method,
            'secret' => $secret,
        ];
    }

    /**
     * @return array{method: MfaMethod, backup_codes: array<int, string>}
     */
    public function confirmTotpEnrollment(Authenticatable $user, string $code, ?Tenant $tenant = null): array
    {
        $this->ensureTotpEnabled();

        $tenant ??= TenantContext::getTenant();
        $method = $this->getTotpMethod($user, $tenant, false);

        if (! $method || ! $method->secret) {
            throw new \RuntimeException('تنظیمات TOTP یافت نشد.');
        }

        if (! app(Google2FA::class)->verifyKey($method->secret, $code)) {
            $this->securityEventService->record('mfa.totp.verify_failed', 'warning', $user, $tenant, [
                'method_id' => $method->getKey(),
            ]);

            throw new \RuntimeException('کد TOTP نامعتبر است.');
        }

        $backupCodes = $this->generateBackupCodes();
        $method->update([
            'enabled_at' => now(),
            'backup_codes' => array_map(fn (string $code) => hash('sha256', $code), $backupCodes),
            'last_used_at' => now(),
        ]);

        $this->securityEventService->record('mfa.totp.enabled', 'info', $user, $tenant, [
            'method_id' => $method->getKey(),
        ]);

        return [
            'method' => $method,
            'backup_codes' => $backupCodes,
        ];
    }

    public function verifyTotp(Authenticatable $user, string $code, ?Tenant $tenant = null): bool
    {
        $this->ensureTotpEnabled();

        $tenant ??= TenantContext::getTenant();
        $method = $this->getTotpMethod($user, $tenant, true);

        if (! $method || ! $method->secret) {
            return false;
        }

        $valid = app(Google2FA::class)->verifyKey($method->secret, $code);
        if ($valid) {
            $method->update(['last_used_at' => now()]);
        }

        return $valid;
    }

    public function verifyBackupCode(Authenticatable $user, string $code, ?Tenant $tenant = null): bool
    {
        $tenant ??= TenantContext::getTenant();
        $method = $this->getTotpMethod($user, $tenant, true);

        if (! $method) {
            return false;
        }

        $codes = $method->backup_codes ?? [];
        $hash = hash('sha256', $code);

        if (! in_array($hash, $codes, true)) {
            return false;
        }

        $method->update([
            'backup_codes' => array_values(array_filter($codes, fn (string $existing) => $existing !== $hash)),
        ]);

        $this->securityEventService->record('mfa.backup_used', 'info', $user, $tenant, [
            'method_id' => $method->getKey(),
        ]);

        return true;
    }

    public function reset(Authenticatable $user, ?Tenant $tenant = null, ?Authenticatable $actor = null, ?string $reason = null): void
    {
        $tenant ??= TenantContext::getTenant();

        $method = $this->getTotpMethod($user, $tenant, false);
        if (! $method) {
            return;
        }

        $method->update([
            'revoked_at' => now(),
            'enabled_at' => null,
        ]);

        $this->securityEventService->record('mfa.reset', 'warning', $actor ?? $user, $tenant, [
            'target_id' => $user->getAuthIdentifier(),
            'reason' => $reason,
        ]);
    }

    protected function getTotpMethod(Authenticatable $user, ?Tenant $tenant, bool $onlyEnabled): ?MfaMethod
    {
        $query = MfaMethod::query()
            ->where('user_id', $user->getAuthIdentifier())
            ->where('type', 'totp')
            ->whereNull('revoked_at');

        if ($tenant) {
            $query->where('tenant_id', $tenant->getKey());
        }

        if ($onlyEnabled) {
            $query->whereNotNull('enabled_at');
        }

        return $query->orderByDesc('id')->first();
    }

    /**
     * @return array<int, string>
     */
    protected function generateBackupCodes(): array
    {
        $count = (int) config('filamat-iam.mfa.backup_codes.count', 8);

        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = strtoupper(Str::random(10));
        }

        return $codes;
    }

    protected function ensureTotpEnabled(): void
    {
        if (! (bool) config('filamat-iam.mfa.totp.enabled', true)) {
            throw new \RuntimeException('TOTP غیرفعال است.');
        }
    }
}
