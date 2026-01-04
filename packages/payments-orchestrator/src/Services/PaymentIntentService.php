<?php

namespace Haida\PaymentsOrchestrator\Services;

use Haida\CommerceOrders\Models\Order;
use Haida\PaymentsOrchestrator\Models\PaymentGatewayConnection;
use Haida\PaymentsOrchestrator\Models\PaymentIntent;
use Illuminate\Validation\ValidationException;

class PaymentIntentService
{
    public function __construct(protected GatewayRegistry $registry)
    {
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function createIntent(Order $order, PaymentGatewayConnection $connection, array $payload): PaymentIntent
    {
        $idempotencyKey = $payload['idempotency_key'] ?? null;
        if (! $idempotencyKey) {
            throw ValidationException::withMessages(['idempotency_key' => 'کلید یکتا الزامی است.']);
        }

        $existing = PaymentIntent::query()
            ->where('tenant_id', $order->tenant_id)
            ->where('idempotency_key', $idempotencyKey)
            ->first();

        if ($existing) {
            return $existing;
        }

        $meta = array_merge($payload['meta'] ?? [], [
            'connection_id' => $connection->getKey(),
        ]);

        $intent = PaymentIntent::query()->create([
            'tenant_id' => $order->tenant_id,
            'site_id' => $order->site_id,
            'order_id' => $order->getKey(),
            'provider_key' => $connection->provider_key,
            'status' => 'pending',
            'currency' => $order->currency,
            'amount' => $order->total,
            'idempotency_key' => $idempotencyKey,
            'meta' => $meta,
        ]);

        $adapter = $this->registry->get($connection->provider_key);
        $response = $adapter->createIntent($intent, array_merge($payload, [
            'connection_id' => $connection->getKey(),
        ]));

        $intent->update([
            'provider_reference' => $response->providerReference,
            'redirect_url' => $response->redirectUrl,
            'status' => $response->status,
            'meta' => array_merge($intent->meta ?? [], $response->meta),
        ]);

        return $intent->refresh();
    }
}
