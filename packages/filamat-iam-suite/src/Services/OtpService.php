<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services;

use Carbon\Carbon;
use Filamat\IamSuite\Models\OtpCode;
use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class OtpService
{
    public function __construct(
        protected NotificationService $notificationService,
        protected SecurityEventService $securityEventService,
        protected \Filamat\IamSuite\Contracts\NotificationAdapter $notificationAdapter
    ) {}

    public function create(Authenticatable $user, string $purpose, ?Tenant $tenant = null): OtpCode
    {
        $tenant ??= TenantContext::getTenant();
        $code = $this->generateCode();

        $otp = OtpCode::query()->create([
            'tenant_id' => $tenant?->getKey(),
            'user_id' => $user->getAuthIdentifier(),
            'code_hash' => $this->hashCode($code),
            'purpose' => $purpose,
            'expires_at' => now()->addMinutes((int) config('filamat-iam.otp.expires_minutes', 5)),
            'attempts' => 0,
        ]);

        $this->securityEventService->record('otp.requested', 'info', $user, $tenant, [
            'purpose' => $purpose,
        ]);

        $this->notificationService->queueOtp($otp, $code);

        return $otp;
    }

    public function verify(Authenticatable $user, string $purpose, string $code, ?Tenant $tenant = null): bool
    {
        $tenant ??= TenantContext::getTenant();
        $rateLimitKey = $this->rateLimitKey($user, $purpose, $tenant);
        $rateLimitConfig = (array) config('filamat-iam.otp.rate_limit', []);
        $rateLimitMax = (int) ($rateLimitConfig['max_attempts'] ?? 10);
        $rateLimitDecay = (int) ($rateLimitConfig['decay_seconds'] ?? 60);

        if (RateLimiter::tooManyAttempts($rateLimitKey, $rateLimitMax)) {
            $this->securityEventService->record('otp.rate_limited', 'warning', $user, $tenant, [
                'purpose' => $purpose,
            ]);

            return false;
        }

        $otp = OtpCode::query()
            ->where('user_id', $user->getAuthIdentifier())
            ->where('tenant_id', $tenant?->getKey())
            ->where('purpose', $purpose)
            ->whereNull('consumed_at')
            ->orderByDesc('id')
            ->first();

        if (! $otp) {
            return false;
        }

        if ($otp->locked_until && $otp->locked_until->isFuture()) {
            return false;
        }

        if ($otp->expires_at && $otp->expires_at->isPast()) {
            return false;
        }

        if (! hash_equals($otp->code_hash, $this->hashCode($code))) {
            $otp->increment('attempts');
            RateLimiter::hit($rateLimitKey, $rateLimitDecay);

            $maxAttempts = (int) config('filamat-iam.otp.max_attempts', 5);
            if ($otp->attempts >= $maxAttempts) {
                $otp->update(['locked_until' => now()->addMinutes((int) config('filamat-iam.otp.lock_minutes', 15))]);
                $this->securityEventService->record('otp.locked', 'warning', $user, $tenant, [
                    'purpose' => $purpose,
                ]);
            } else {
                $this->securityEventService->record('otp.failed', 'warning', $user, $tenant, [
                    'purpose' => $purpose,
                    'attempts' => $otp->attempts,
                ]);
            }

            return false;
        }

        if ((bool) config('filamat-iam.otp.verify_via_adapter', true)) {
            $adapterOk = $this->notificationAdapter->verifyOtp($user, $purpose, $code, [
                'tenant_id' => $tenant?->getKey(),
            ]);
            if (! $adapterOk) {
                $this->securityEventService->record('otp.adapter_denied', 'warning', $user, $tenant, [
                    'purpose' => $purpose,
                ]);

                return false;
            }
        }

        RateLimiter::clear($rateLimitKey);
        $otp->update(['consumed_at' => Carbon::now()]);
        $this->securityEventService->record('otp.verified', 'info', $user, $tenant, [
            'purpose' => $purpose,
        ]);

        return true;
    }

    protected function generateCode(): string
    {
        $length = (int) config('filamat-iam.otp.length', 6);
        $digits = '';
        for ($i = 0; $i < $length; $i++) {
            $digits .= (string) random_int(0, 9);
        }

        return $digits;
    }

    protected function hashCode(string $code): string
    {
        return hash('sha256', $code.Str::of(config('app.key'))->toString());
    }

    protected function rateLimitKey(Authenticatable $user, string $purpose, ?Tenant $tenant): string
    {
        return implode(':', [
            'otp',
            (string) $user->getAuthIdentifier(),
            (string) ($tenant?->getKey() ?? 'global'),
            $purpose,
        ]);
    }
}
