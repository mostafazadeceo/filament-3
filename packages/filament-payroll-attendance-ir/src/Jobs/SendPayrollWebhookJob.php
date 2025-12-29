<?php

namespace Vendor\FilamentPayrollAttendanceIr\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollWebhookDelivery;

class SendPayrollWebhookJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(private readonly int $deliveryId) {}

    public function handle(): void
    {
        $delivery = PayrollWebhookDelivery::query()->with('subscription')->find($this->deliveryId);
        if (! $delivery || ! $delivery->subscription || ! $delivery->subscription->is_active) {
            return;
        }

        $subscription = $delivery->subscription;
        $payload = $delivery->payload ?? [];
        $signature = $subscription->secret
            ? hash_hmac('sha256', json_encode($payload, JSON_UNESCAPED_UNICODE), $subscription->secret)
            : null;

        $response = Http::withHeaders(array_filter([
            'X-Payroll-Signature' => $signature,
            'X-Payroll-Event' => $delivery->event,
        ]))->post($subscription->url, $payload);

        $delivery->update([
            'status' => $response->successful() ? 'delivered' : 'failed',
            'response_code' => $response->status(),
            'response_body' => $response->body(),
            'delivered_at' => now(),
        ]);

        $subscription->update([
            'last_delivery_at' => now(),
        ]);
    }
}
