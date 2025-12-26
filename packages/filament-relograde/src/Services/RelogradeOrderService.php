<?php

namespace Haida\FilamentRelograde\Services;

use Haida\FilamentRelograde\Clients\RelogradeClientFactory;
use Haida\FilamentRelograde\Enums\OrderStatus;
use Haida\FilamentRelograde\Models\RelogradeConnection;
use Haida\FilamentRelograde\Models\RelogradeOrder;
use RuntimeException;

class RelogradeOrderService
{
    public function __construct(
        protected RelogradeClientFactory $clientFactory,
        protected RelogradeOrderSynchronizer $synchronizer,
        protected RelogradeAuditLogger $auditLogger,
    ) {}

    public function createOrder(RelogradeConnection $connection, array $payload, ?string $fulfillmentPolicy = null): RelogradeOrder
    {
        $client = $this->clientFactory->make($connection);
        $orderPayload = $client->createOrder($payload);

        $order = $this->synchronizer->sync($connection, $orderPayload);

        $this->auditLogger->log('orders.create', $connection, [
            'entity_type' => RelogradeOrder::class,
            'entity_id' => $order->trx,
            'payload' => [
                'reference' => $order->reference,
                'payment_currency' => $order->payment_currency,
            ],
        ]);

        if ($fulfillmentPolicy === 'confirm') {
            return $this->confirmOrder($order);
        }

        if ($fulfillmentPolicy === 'resolve') {
            return $this->resolveOrder($order);
        }

        return $order;
    }

    public function confirmOrder(RelogradeOrder $order): RelogradeOrder
    {
        $connection = $order->connection;
        $client = $this->clientFactory->make($connection);

        $payload = $client->confirmOrder($order->trx);

        $updated = $this->synchronizer->sync($connection, $payload);

        $this->auditLogger->log('orders.confirm', $connection, [
            'entity_type' => RelogradeOrder::class,
            'entity_id' => $order->trx,
        ]);

        return $updated;
    }

    public function resolveOrder(RelogradeOrder $order): RelogradeOrder
    {
        if ($order->items()->count() > 1) {
            throw new RuntimeException('نهایی‌سازی فقط برای سفارش‌های تک‌قلم مجاز است.');
        }

        $connection = $order->connection;
        $client = $this->clientFactory->make($connection);

        $payload = $client->resolveOrder($order->trx);

        $updated = $this->synchronizer->sync($connection, $payload);

        $this->auditLogger->log('orders.resolve', $connection, [
            'entity_type' => RelogradeOrder::class,
            'entity_id' => $order->trx,
        ]);

        return $updated;
    }

    public function cancelOrder(RelogradeOrder $order): RelogradeOrder
    {
        $connection = $order->connection;
        $client = $this->clientFactory->make($connection);

        $payload = $client->cancelOrder($order->trx);

        if (empty($payload)) {
            $order->order_status = OrderStatus::Deleted->value;
            $order->last_synced_at = now();
            $order->save();

            $this->auditLogger->log('orders.cancel', $connection, [
                'entity_type' => RelogradeOrder::class,
                'entity_id' => $order->trx,
                'payload' => [
                    'status' => OrderStatus::Deleted->value,
                ],
            ]);

            return $order;
        }

        $updated = $this->synchronizer->sync($connection, $payload);

        $this->auditLogger->log('orders.cancel', $connection, [
            'entity_type' => RelogradeOrder::class,
            'entity_id' => $order->trx,
            'payload' => [
                'status' => $updated->order_status,
            ],
        ]);

        return $updated;
    }

    public function refreshOrder(RelogradeOrder $order): RelogradeOrder
    {
        $connection = $order->connection;
        $client = $this->clientFactory->make($connection);

        $payload = $client->findOrder($order->trx);

        $updated = $this->synchronizer->sync($connection, $payload);

        $this->auditLogger->log('orders.refresh', $connection, [
            'entity_type' => RelogradeOrder::class,
            'entity_id' => $order->trx,
        ]);

        return $updated;
    }
}
