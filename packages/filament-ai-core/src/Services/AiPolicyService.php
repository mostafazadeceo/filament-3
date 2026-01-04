<?php

namespace Haida\FilamentAiCore\Services;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentAiCore\Models\AiPolicy;

class AiPolicyService
{
    /**
     * @return array<string, mixed>
     */
    public function resolvePolicy(?Tenant $tenant = null): array
    {
        $tenant ??= TenantContext::getTenant();
        $tenantId = $tenant?->getKey();

        $defaults = [
            'enabled' => (bool) config('filament-ai-core.enabled', false),
            'provider' => (string) config('filament-ai-core.default_provider', 'mock'),
            'redaction_policy' => (array) config('filament-ai-core.redaction.defaults', []),
            'retention_days' => (int) config('filament-ai-core.retention_days', 30),
            'consent_required_meetings' => (bool) config('filament-ai-core.consent_required_meetings', true),
            'allow_store_transcripts' => (bool) config('filament-ai-core.allow_store_transcripts', false),
            'tenant_id' => $tenantId,
        ];

        if (! $tenantId) {
            return $defaults;
        }

        $policy = AiPolicy::query()->where('tenant_id', $tenantId)->first();
        if (! $policy) {
            return $defaults;
        }

        return [
            'enabled' => (bool) $policy->enabled,
            'provider' => (string) ($policy->provider ?: $defaults['provider']),
            'redaction_policy' => $policy->redaction_policy ?: $defaults['redaction_policy'],
            'retention_days' => (int) ($policy->retention_days ?? $defaults['retention_days']),
            'consent_required_meetings' => (bool) ($policy->consent_required_meetings ?? $defaults['consent_required_meetings']),
            'allow_store_transcripts' => (bool) ($policy->allow_store_transcripts ?? $defaults['allow_store_transcripts']),
            'tenant_id' => $tenantId,
            'policy_id' => $policy->getKey(),
        ];
    }
}
