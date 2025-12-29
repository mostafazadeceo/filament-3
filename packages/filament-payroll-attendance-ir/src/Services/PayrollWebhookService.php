<?php

namespace Vendor\FilamentPayrollAttendanceIr\Services;

use Vendor\FilamentPayrollAttendanceIr\Jobs\SendPayrollWebhookJob;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollWebhookDelivery;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollWebhookSubscription;

class PayrollWebhookService
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function dispatch(string $event, int $companyId, array $payload): void
    {
        $subscriptions = PayrollWebhookSubscription::query()
            ->where('company_id', $companyId)
            ->where('is_active', true)
            ->get();

        foreach ($subscriptions as $subscription) {
            $events = $subscription->events ?? [];
            if ($events && ! in_array($event, $events, true)) {
                continue;
            }

            $delivery = PayrollWebhookDelivery::query()->create([
                'tenant_id' => $subscription->tenant_id,
                'company_id' => $subscription->company_id,
                'subscription_id' => $subscription->getKey(),
                'event' => $event,
                'payload' => $payload,
                'status' => 'pending',
            ]);

            SendPayrollWebhookJob::dispatch($delivery->getKey());
        }
    }
}
