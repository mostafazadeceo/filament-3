<?php

namespace Haida\FilamentCommerceExperience\Services;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentCommerceExperience\Models\ExperienceBuyNowPreference;
use Illuminate\Database\DatabaseManager;
use Illuminate\Validation\ValidationException;

class BuyNowService
{
    public function __construct(protected DatabaseManager $db) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public function enable(array $payload, ?string $consentIp = null): ExperienceBuyNowPreference
    {
        if (! config('filament-commerce-experience.buy_now.enabled', true)) {
            throw ValidationException::withMessages(['buy_now' => 'خرید فوری فعال نیست.']);
        }

        $tenantId = $payload['tenant_id'] ?? TenantContext::getTenantId();
        if (! $tenantId) {
            throw ValidationException::withMessages(['tenant_id' => 'شناسه تننت الزامی است.']);
        }

        $customerId = $payload['customer_id'] ?? null;
        if (! $customerId) {
            throw ValidationException::withMessages(['customer_id' => 'شناسه مشتری الزامی است.']);
        }

        return $this->db->transaction(function () use ($payload, $tenantId, $customerId, $consentIp): ExperienceBuyNowPreference {
            return ExperienceBuyNowPreference::query()->updateOrCreate([
                'tenant_id' => $tenantId,
                'customer_id' => $customerId,
            ], [
                'default_address_id' => $payload['default_address_id'] ?? null,
                'default_payment_provider' => $payload['default_payment_provider'] ?? null,
                'status' => 'active',
                'requires_2fa' => (bool) ($payload['requires_2fa'] ?? config('filament-commerce-experience.buy_now.requires_2fa', false)),
                'consent_at' => $payload['consent_at'] ?? now(),
                'consent_ip' => $consentIp,
                'metadata' => $payload['metadata'] ?? null,
            ]);
        });
    }

    public function disable(ExperienceBuyNowPreference $preference): ExperienceBuyNowPreference
    {
        $preference->update([
            'status' => 'revoked',
        ]);

        return $preference->refresh();
    }
}
