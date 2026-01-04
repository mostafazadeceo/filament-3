<?php

namespace Haida\FilamentLoyaltyClub\Adapters;

use Carbon\CarbonImmutable;
use Haida\CommerceOrders\Models\Order;
use Haida\FilamentLoyaltyClub\Contracts\PurchaseAdapterInterface;
use Haida\FilamentLoyaltyClub\Support\PurchaseData;

class CommerceOrdersAdapter implements PurchaseAdapterInterface
{
    public function resolve(array $payload): PurchaseData
    {
        $orderId = $payload['order_id'] ?? $payload['orderId'] ?? null;
        if ($orderId && class_exists(Order::class)) {
            $order = Order::query()->find($orderId);
            if ($order) {
                $occurredAt = $order->paid_at ?: $order->placed_at;
                $occurredAt = $occurredAt ? CarbonImmutable::parse($occurredAt) : null;

                return new PurchaseData(
                    (float) $order->total,
                    (string) $order->currency,
                    (string) ($order->number ?? $order->getKey()),
                    $occurredAt,
                    [
                        'order_id' => $order->getKey(),
                        'status' => $order->status,
                        'payment_status' => $order->payment_status,
                    ]
                );
            }
        }

        $fallback = new FallbackPurchaseAdapter;

        return $fallback->resolve($payload);
    }
}
