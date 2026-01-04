<?php

declare(strict_types=1);

namespace Haida\FilamentRelograde\Adapters;

use Haida\FilamentRelograde\Jobs\ProcessWebhookEventJob;
use Haida\FilamentRelograde\Models\RelogradeConnection;
use Haida\FilamentRelograde\Models\RelogradeOrder;
use Haida\FilamentRelograde\Models\RelogradeWebhookEvent;
use Haida\FilamentRelograde\Services\RelogradeOrderService;
use Haida\FilamentRelograde\Services\RelogradeSyncService;
use Haida\ProvidersCore\Contracts\ProviderAdapter;
use Haida\ProvidersCore\DataTransferObjects\ProviderContext;
use Haida\ProvidersCore\DataTransferObjects\ProviderResult;

class RelogradeProviderAdapter implements ProviderAdapter
{
    public function __construct(
        protected RelogradeSyncService $syncService,
        protected RelogradeOrderService $orderService,
    ) {}

    public function key(): string
    {
        return 'relograde';
    }

    public function label(): string
    {
        return 'Relograde';
    }

    public function supportsSandbox(): bool
    {
        return true;
    }

    public function syncProducts(ProviderContext $context, array $payload = []): ProviderResult
    {
        $connection = $this->resolveConnection($context->connectionId);
        if (! $connection) {
            return ProviderResult::fail('Connection not found.');
        }

        $count = $this->syncService->syncProducts($connection, (bool) ($payload['full_sync'] ?? true));

        return ProviderResult::ok(['synced' => $count]);
    }

    public function syncInventory(ProviderContext $context, array $payload = []): ProviderResult
    {
        return ProviderResult::fail('Inventory sync is not supported for this provider.');
    }

    public function createOrder(ProviderContext $context, array $payload): ProviderResult
    {
        $connection = $this->resolveConnection($context->connectionId);
        if (! $connection) {
            return ProviderResult::fail('Connection not found.');
        }

        $policy = $payload['fulfillment_policy'] ?? null;
        $order = $this->orderService->createOrder($connection, $payload, is_string($policy) ? $policy : null);

        return ProviderResult::ok([
            'order_id' => $order->getKey(),
            'status' => $order->status,
            'trx' => $order->trx,
        ]);
    }

    public function fulfillOrder(ProviderContext $context, array $payload): ProviderResult
    {
        $order = $this->resolveOrder($payload);
        if (! $order) {
            return ProviderResult::fail('Order not found.');
        }

        $action = (string) ($payload['action'] ?? 'confirm');

        $updated = match ($action) {
            'resolve' => $this->orderService->resolveOrder($order),
            'cancel' => $this->orderService->cancelOrder($order),
            default => $this->orderService->confirmOrder($order),
        };

        return ProviderResult::ok([
            'order_id' => $updated->getKey(),
            'status' => $updated->status,
            'trx' => $updated->trx,
        ]);
    }

    public function fetchOrderStatus(ProviderContext $context, array $payload): ProviderResult
    {
        $order = $this->resolveOrder($payload);
        if (! $order) {
            return ProviderResult::fail('Order not found.');
        }

        $updated = $this->orderService->refreshOrder($order);

        return ProviderResult::ok([
            'order_id' => $updated->getKey(),
            'status' => $updated->status,
            'trx' => $updated->trx,
        ]);
    }

    public function handleWebhook(ProviderContext $context, array $payload): ProviderResult
    {
        $connection = $this->resolveConnection($context->connectionId);

        $eventPayload = $payload['payload'] ?? $payload;

        $event = RelogradeWebhookEvent::create([
            'connection_id' => $connection?->getKey(),
            'event' => $payload['event'] ?? data_get($eventPayload, 'event'),
            'state' => $payload['state'] ?? data_get($eventPayload, 'state'),
            'api_key_description' => $payload['api_key_description'] ?? data_get($eventPayload, 'apiKeyDescription'),
            'trx' => $payload['trx'] ?? data_get($eventPayload, 'data.trx'),
            'reference' => $payload['reference'] ?? data_get($eventPayload, 'data.reference'),
            'payload' => $eventPayload,
            'received_ip' => $payload['received_ip'] ?? null,
            'processing_status' => 'pending',
        ]);

        ProcessWebhookEventJob::dispatch($event->getKey());

        return ProviderResult::ok(['webhook_event_id' => $event->getKey()]);
    }

    protected function resolveConnection(?int $connectionId): ?RelogradeConnection
    {
        if ($connectionId) {
            return RelogradeConnection::query()->find($connectionId);
        }

        return RelogradeConnection::query()->default()->first();
    }

    protected function resolveOrder(array $payload): ?RelogradeOrder
    {
        $orderId = $payload['order_id'] ?? null;
        if ($orderId) {
            return RelogradeOrder::query()->find($orderId);
        }

        $trx = $payload['trx'] ?? null;
        if ($trx) {
            return RelogradeOrder::query()->where('trx', $trx)->first();
        }

        return null;
    }
}
