<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services;

use Filamat\IamSuite\Events\SubscriptionChanged;
use Filamat\IamSuite\Models\Subscription;
use Filamat\IamSuite\Models\SubscriptionPlan;
use Filamat\IamSuite\Models\Tenant;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Events\Dispatcher;

class SubscriptionService
{
    public function __construct(protected Dispatcher $events) {}

    public function subscribe(Tenant $tenant, SubscriptionPlan $plan, ?Authenticatable $user = null): Subscription
    {
        $status = $plan->trial_days ? 'trialing' : 'active';

        $subscription = Subscription::query()->create([
            'tenant_id' => $tenant->getKey(),
            'user_id' => $user?->getAuthIdentifier(),
            'plan_id' => $plan->getKey(),
            'status' => $status,
            'trial_ends_at' => $plan->trial_days ? now()->addDays($plan->trial_days) : null,
            'renews_at' => now()->addDays($plan->period_days),
            'provider' => 'dummy',
        ]);

        $this->events->dispatch(new SubscriptionChanged($subscription));

        return $subscription;
    }

    public function cancel(Subscription $subscription, string $reason = 'cancelled'): Subscription
    {
        $subscription->update([
            'status' => 'cancelled',
            'ends_at' => now(),
            'meta' => array_merge($subscription->meta ?? [], ['cancel_reason' => $reason]),
        ]);

        $this->events->dispatch(new SubscriptionChanged($subscription));

        return $subscription;
    }
}
