<?php

namespace Haida\FilamentCryptoCore\Services;

use Filamat\IamSuite\Models\Subscription;
use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentCryptoCore\Models\CryptoFeePolicy;

class FeePolicyEngine
{
    public function resolvePlanKey(?int $tenantId = null): string
    {
        $tenantId ??= TenantContext::getTenantId();
        $subscription = $this->resolveSubscription($tenantId);

        if (! $subscription || ! $subscription->plan) {
            return 'free';
        }

        $features = $subscription->plan->features;
        if (is_array($features) && isset($features['crypto_plan'])) {
            return (string) $features['crypto_plan'];
        }

        return (string) ($subscription->plan->code ?? 'free');
    }

    /**
     * @return array<string, mixed>
     */
    public function policyForTenant(?int $tenantId = null): array
    {
        $tenantId ??= TenantContext::getTenantId();
        $planKey = $this->resolvePlanKey($tenantId);

        $policy = CryptoFeePolicy::query()
            ->where('tenant_id', $tenantId)
            ->where('plan_key', $planKey)
            ->first();

        if ($policy) {
            return [
                'plan' => $planKey,
                'fees' => [
                    'invoice_percent' => (float) $policy->invoice_percent,
                    'invoice_fixed' => (float) $policy->invoice_fixed,
                    'payout_fixed' => (float) $policy->payout_fixed,
                    'conversion_percent' => (float) $policy->conversion_percent,
                    'network_fee_mode' => $policy->network_fee_mode,
                ],
                'meta' => $policy->meta ?? [],
            ];
        }

        $default = (array) config('filament-crypto-core.plans.'.$planKey, []);

        return [
            'plan' => $planKey,
            'fees' => $default['fees'] ?? [],
            'meta' => $default['meta'] ?? [],
        ];
    }

    public function featureEnabled(string $key, ?int $tenantId = null): bool
    {
        $tenantId ??= TenantContext::getTenantId();
        $planKey = $this->resolvePlanKey($tenantId);
        $subscription = $this->resolveSubscription($tenantId);

        $features = [];
        if ($subscription && $subscription->plan && is_array($subscription->plan->features)) {
            $features = $subscription->plan->features['crypto_features']
                ?? $subscription->plan->features['crypto']
                ?? [];
        }

        if ($features === []) {
            $features = (array) (config('filament-crypto-core.plans.'.$planKey.'.features') ?? []);
        }

        return (bool) ($features[$key] ?? false);
    }

    protected function resolveSubscription(?int $tenantId): ?Subscription
    {
        if (! $tenantId) {
            return null;
        }

        $statuses = (array) config('filamat-iam.subscriptions.active_statuses', ['active', 'trialing']);

        return Subscription::query()
            ->where('tenant_id', $tenantId)
            ->whereIn('status', $statuses)
            ->with('plan')
            ->orderByRaw('user_id is not null desc')
            ->first();
    }
}
