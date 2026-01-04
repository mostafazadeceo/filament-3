<?php

namespace Haida\FeatureGates\Services;

use Carbon\CarbonInterface;
use Filamat\IamSuite\Models\Subscription;
use Filamat\IamSuite\Models\SubscriptionPlan;
use Filamat\IamSuite\Models\Tenant;
use Haida\FeatureGates\Models\PlanFeature;
use Haida\FeatureGates\Models\TenantFeatureOverride;
use Haida\FeatureGates\Support\FeatureGateDecision;
use Illuminate\Contracts\Auth\Authenticatable;

class FeatureGateService
{
    public function evaluate(
        Tenant $tenant,
        string $featureKey,
        ?Subscription $subscription = null,
        ?Authenticatable $user = null,
        ?CarbonInterface $at = null,
    ): FeatureGateDecision {
        $now = $at ?? now();

        $override = TenantFeatureOverride::query()
            ->where('tenant_id', $tenant->getKey())
            ->where('feature_key', $featureKey)
            ->orderByDesc('id')
            ->first();

        if ($override && $this->withinWindow($override->starts_at, $override->ends_at, $now)) {
            return new FeatureGateDecision(
                $override->allowed,
                'tenant_override',
                $override->allowed ? 'Tenant override allows access.' : 'Tenant override denies access.',
                $override->limits,
            );
        }

        $subscription ??= $this->resolveActiveSubscription($tenant, $user);
        if (! $subscription || ! $subscription->plan) {
            return new FeatureGateDecision(false, 'subscription', 'No active subscription found.');
        }

        $planFeature = PlanFeature::query()
            ->where('plan_id', $subscription->plan->getKey())
            ->where('feature_key', $featureKey)
            ->first();

        if ($planFeature) {
            if (! $this->withinWindow($planFeature->starts_at, $planFeature->ends_at, $now)) {
                return new FeatureGateDecision(false, 'plan_feature', 'Plan feature window is inactive.', $planFeature->limits);
            }

            return new FeatureGateDecision(
                $planFeature->enabled,
                'plan_feature',
                $planFeature->enabled ? 'Plan feature enabled.' : 'Plan feature disabled.',
                $planFeature->limits,
            );
        }

        $fallback = $this->fallbackFromPlanFeatures($subscription->plan, $featureKey);
        if ($fallback !== null) {
            return new FeatureGateDecision($fallback, 'plan_features_json', 'Feature resolved from plan JSON.');
        }

        return new FeatureGateDecision(true, 'default', 'No specific restrictions.');
    }

    private function resolveActiveSubscription(?Tenant $tenant, ?Authenticatable $user): ?Subscription
    {
        $statuses = (array) config('filamat-iam.subscriptions.active_statuses', ['active', 'trialing']);

        $query = Subscription::query()
            ->where('tenant_id', $tenant?->getKey())
            ->whereIn('status', $statuses)
            ->orderByRaw('user_id is not null desc')
            ->with('plan');

        if ($user) {
            $query->where(function ($builder) use ($user) {
                $builder->whereNull('user_id')
                    ->orWhere('user_id', $user->getAuthIdentifier());
            });
        }

        return $query->first();
    }

    private function withinWindow(?CarbonInterface $startsAt, ?CarbonInterface $endsAt, CarbonInterface $now): bool
    {
        if ($startsAt && $startsAt->greaterThan($now)) {
            return false;
        }

        if ($endsAt && $endsAt->lessThanOrEqualTo($now)) {
            return false;
        }

        return true;
    }

    private function fallbackFromPlanFeatures(SubscriptionPlan $plan, string $featureKey): ?bool
    {
        $features = $plan->features ?? [];
        if (! is_array($features)) {
            return null;
        }

        if (array_key_exists($featureKey, $features) && is_bool($features[$featureKey])) {
            return $features[$featureKey];
        }

        $list = $features['features'] ?? $features['permissions'] ?? null;
        if (is_array($list)) {
            return in_array($featureKey, $list, true);
        }

        return null;
    }
}
