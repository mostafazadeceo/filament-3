<?php

declare(strict_types=1);

namespace Haida\ProvidersEsimGoCore\Adapters;

use Haida\ProvidersCore\Contracts\ProviderAdapter;
use Haida\ProvidersCore\DataTransferObjects\ProviderContext;
use Haida\ProvidersCore\DataTransferObjects\ProviderResult;
use Haida\ProvidersEsimGoCore\Models\EsimGoConnection;
use Haida\ProvidersEsimGoCore\Models\EsimGoOrder;
use Haida\ProvidersEsimGoCore\Services\EsimGoCatalogueService;
use Haida\ProvidersEsimGoCore\Services\EsimGoInventoryService;
use Haida\ProvidersEsimGoCore\Services\EsimGoOrderService;
use Haida\ProvidersEsimGoCore\Services\EsimGoWebhookService;

class EsimGoProviderAdapter implements ProviderAdapter
{
    public function __construct(
        protected EsimGoCatalogueService $catalogueService,
        protected EsimGoInventoryService $inventoryService,
        protected EsimGoOrderService $orderService,
        protected EsimGoWebhookService $webhookService,
    ) {}

    public function key(): string
    {
        return 'esim-go';
    }

    public function label(): string
    {
        return 'eSIM Go';
    }

    public function supportsSandbox(): bool
    {
        return true;
    }

    public function syncProducts(ProviderContext $context, array $payload = []): ProviderResult
    {
        $connection = $this->resolveConnection($context->connectionId);
        if (! $connection) {
            return ProviderResult::fail('اتصال پیدا نشد.');
        }

        $filters = $payload['filters'] ?? [];
        try {
            $count = $this->catalogueService->sync($connection, is_array($filters) ? $filters : [], (bool) ($payload['force'] ?? false), $context->sandbox);
        } catch (\Throwable $exception) {
            return ProviderResult::fail($exception->getMessage());
        }

        return ProviderResult::ok(['synced' => $count]);
    }

    public function syncInventory(ProviderContext $context, array $payload = []): ProviderResult
    {
        $connection = $this->resolveConnection($context->connectionId);
        if (! $connection) {
            return ProviderResult::fail('اتصال پیدا نشد.');
        }

        $filters = $payload['filters'] ?? [];
        try {
            $count = $this->inventoryService->sync($connection, is_array($filters) ? $filters : [], $context->sandbox);
        } catch (\Throwable $exception) {
            return ProviderResult::fail($exception->getMessage());
        }

        return ProviderResult::ok(['synced' => $count]);
    }

    public function createOrder(ProviderContext $context, array $payload): ProviderResult
    {
        $connection = $this->resolveConnection($context->connectionId);
        if (! $connection) {
            return ProviderResult::fail('اتصال پیدا نشد.');
        }

        $commerceOrderId = isset($payload['commerce_order_id']) ? (int) $payload['commerce_order_id'] : null;

        $order = $this->orderService->createProviderOrder($connection, $payload, $commerceOrderId, $context->sandbox);

        return ProviderResult::ok([
            'order_id' => $order->getKey(),
            'status' => $order->status,
            'reference' => $order->provider_reference,
        ]);
    }

    public function fulfillOrder(ProviderContext $context, array $payload): ProviderResult
    {
        $order = $this->resolveOrder($payload);
        if (! $order) {
            return ProviderResult::fail('سفارش پیدا نشد.');
        }

        $order = $this->orderService->refreshAssignments($order, $context->sandbox);

        return ProviderResult::ok([
            'order_id' => $order->getKey(),
            'status' => $order->status,
        ]);
    }

    public function fetchOrderStatus(ProviderContext $context, array $payload): ProviderResult
    {
        $order = $this->resolveOrder($payload);
        if (! $order) {
            return ProviderResult::fail('سفارش پیدا نشد.');
        }

        $order = $this->orderService->refreshAssignments($order, $context->sandbox);

        return ProviderResult::ok([
            'order_id' => $order->getKey(),
            'status' => $order->status,
        ]);
    }

    public function handleWebhook(ProviderContext $context, array $payload): ProviderResult
    {
        $connection = $this->resolveConnection($context->connectionId);
        if (! $connection) {
            return ProviderResult::fail('اتصال پیدا نشد.');
        }

        $rawBody = (string) ($payload['raw_body'] ?? '');
        $signatureValid = (bool) ($payload['signature_valid'] ?? false);
        $bodyPayload = $payload['payload'] ?? $payload;

        $callback = $this->webhookService->ingest($connection, $rawBody, is_array($bodyPayload) ? $bodyPayload : [], $signatureValid);

        return ProviderResult::ok([
            'callback_id' => $callback?->getKey(),
        ]);
    }

    protected function resolveConnection(?int $connectionId): ?EsimGoConnection
    {
        if ($connectionId) {
            return EsimGoConnection::query()->find($connectionId);
        }

        return EsimGoConnection::query()->default()->first();
    }

    protected function resolveOrder(array $payload): ?EsimGoOrder
    {
        $orderId = $payload['order_id'] ?? null;
        if ($orderId) {
            return EsimGoOrder::query()->find($orderId);
        }

        $reference = $payload['reference'] ?? null;
        if ($reference) {
            return EsimGoOrder::query()->where('provider_reference', $reference)->first();
        }

        return null;
    }
}
