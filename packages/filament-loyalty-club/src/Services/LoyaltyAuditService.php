<?php

namespace Haida\FilamentLoyaltyClub\Services;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentLoyaltyClub\Models\LoyaltyAuditEvent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class LoyaltyAuditService
{
    /**
     * @param  array<string, mixed>  $meta
     */
    public function record(string $action, array $meta = [], ?Model $subject = null, ?int $tenantId = null): ?LoyaltyAuditEvent
    {
        $tenantId = $tenantId ?? ($subject?->tenant_id ?? TenantContext::getTenantId());
        if (! $tenantId) {
            return null;
        }

        $actor = Auth::user();
        $actorId = $actor?->getAuthIdentifier();
        $actorType = $actor ? $actor::class : null;

        return LoyaltyAuditEvent::query()->create([
            'tenant_id' => $tenantId,
            'actor_id' => $actorId,
            'actor_type' => $actorType,
            'action' => $action,
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subject?->getKey(),
            'ip_hash' => $this->resolveIpHash(),
            'meta' => $this->redact($meta),
            'occurred_at' => now(),
        ]);
    }

    protected function resolveIpHash(): ?string
    {
        if (! (bool) config('filament-loyalty-club.privacy.store_ip', false)) {
            return null;
        }

        if (! app()->bound('request')) {
            return null;
        }

        $ip = request()->ip();
        if (! $ip) {
            return null;
        }

        return hash('sha256', $ip);
    }

    /**
     * @param  array<string, mixed>  $meta
     * @return array<string, mixed>
     */
    protected function redact(array $meta): array
    {
        if (! (bool) config('filament-loyalty-club.privacy.redact_logs', true)) {
            return $meta;
        }

        foreach (['phone', 'email', 'ip', 'device_id'] as $key) {
            if (array_key_exists($key, $meta)) {
                $meta[$key] = 'redacted';
            }
        }

        return $meta;
    }
}
