<?php

namespace Haida\CommerceOrders\Services;

use Filamat\IamSuite\Services\AuditService;
use Haida\CommerceOrders\Models\Order;
use Haida\CommerceOrders\Models\OrderReturn;
use Haida\CommerceOrders\Models\OrderReturnItem;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Arr;

class OrderReturnService
{
    public function __construct(
        protected DatabaseManager $db,
        protected AuditService $auditService
    ) {}

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @param  array<string, mixed>  $payload
     */
    public function createReturn(Order $order, array $items, array $payload = [], ?Authenticatable $actor = null): OrderReturn
    {
        return $this->db->transaction(function () use ($order, $items, $payload, $actor): OrderReturn {
            $return = OrderReturn::query()->create([
                'tenant_id' => $order->tenant_id,
                'order_id' => $order->getKey(),
                'status' => $payload['status'] ?? 'requested',
                'reason' => $payload['reason'] ?? null,
                'notes' => $payload['notes'] ?? null,
                'requested_at' => $payload['requested_at'] ?? now(),
                'requested_by_user_id' => $actor?->getAuthIdentifier(),
                'meta' => $payload['meta'] ?? null,
            ]);

            $returnItems = array_map(function (array $item) use ($order, $return): array {
                return [
                    'tenant_id' => $order->tenant_id,
                    'order_return_id' => $return->getKey(),
                    'order_item_id' => Arr::get($item, 'order_item_id'),
                    'product_id' => Arr::get($item, 'product_id'),
                    'variant_id' => Arr::get($item, 'variant_id'),
                    'name' => (string) Arr::get($item, 'name', 'آیتم مرجوعی'),
                    'sku' => Arr::get($item, 'sku'),
                    'quantity' => (float) Arr::get($item, 'quantity', 1),
                    'reason' => Arr::get($item, 'reason'),
                    'status' => Arr::get($item, 'status', 'requested'),
                    'meta' => Arr::get($item, 'meta'),
                ];
            }, $items);

            if ($returnItems !== []) {
                OrderReturnItem::query()->insert($returnItems);
            }

            $this->auditService->log('order.return.created', $return, [
                'items' => count($returnItems),
            ], $actor);

            return $return->refresh()->loadMissing('items');
        });
    }

    public function updateStatus(OrderReturn $return, string $status, ?Authenticatable $actor = null, ?string $note = null): OrderReturn
    {
        return $this->db->transaction(function () use ($return, $status, $actor, $note): OrderReturn {
            $payload = ['status' => $status];
            $now = now();

            if ($status === 'approved') {
                $payload['approved_at'] = $now;
                $payload['approved_by_user_id'] = $actor?->getAuthIdentifier();
            }

            if ($status === 'rejected') {
                $payload['rejected_at'] = $now;
                $payload['rejected_by_user_id'] = $actor?->getAuthIdentifier();
            }

            if ($status === 'received') {
                $payload['received_at'] = $now;
                $payload['received_by_user_id'] = $actor?->getAuthIdentifier();
            }

            if ($status === 'refunded') {
                $payload['refunded_at'] = $now;
                $payload['refunded_by_user_id'] = $actor?->getAuthIdentifier();
            }

            if ($note) {
                $payload['notes'] = trim((string) $return->notes.'\n'.$note);
            }

            $return->update($payload);

            $this->auditService->log('order.return.status_updated', $return, [
                'status' => $status,
            ], $actor);

            return $return->refresh();
        });
    }
}
