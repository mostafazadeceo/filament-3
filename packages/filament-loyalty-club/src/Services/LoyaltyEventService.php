<?php

namespace Haida\FilamentLoyaltyClub\Services;

use Haida\FilamentLoyaltyClub\Models\LoyaltyCustomer;
use Haida\FilamentLoyaltyClub\Models\LoyaltyEvent;
use Illuminate\Support\Arr;
use Throwable;

class LoyaltyEventService
{
    public function __construct(protected LoyaltyRuleEngine $ruleEngine) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public function ingest(
        LoyaltyCustomer $customer,
        string $type,
        array $payload,
        string $idempotencyKey,
        ?string $source = null,
    ): LoyaltyEvent {
        $existing = LoyaltyEvent::query()
            ->where('tenant_id', $customer->tenant_id)
            ->where('idempotency_key', $idempotencyKey)
            ->first();

        if ($existing) {
            return $existing;
        }

        $event = LoyaltyEvent::query()->create([
            'tenant_id' => $customer->tenant_id,
            'customer_id' => $customer->getKey(),
            'type' => $type,
            'source' => $source,
            'idempotency_key' => $idempotencyKey,
            'payload' => Arr::wrap($payload),
            'occurred_at' => $payload['occurred_at'] ?? now(),
            'status' => 'pending',
        ]);

        try {
            $this->ruleEngine->process($event);
            $event->status = 'processed';
            $event->processed_at = now();
            $event->save();
        } catch (Throwable $exception) {
            $event->status = 'failed';
            $event->error_message = $exception->getMessage();
            $event->save();
        }

        return $event;
    }
}
