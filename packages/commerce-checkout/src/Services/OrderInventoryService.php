<?php

declare(strict_types=1);

namespace Haida\CommerceCheckout\Services;

use Haida\CommerceOrders\Models\Order;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Vendor\FilamentAccountingIr\Models\InventoryDoc;
use Vendor\FilamentAccountingIr\Models\InventoryItem;
use Vendor\FilamentAccountingIr\Models\InventoryWarehouse;
use Vendor\FilamentAccountingIr\Services\InventoryDocService;

class OrderInventoryService
{
    public function __construct(
        protected DatabaseManager $db,
        protected InventoryDocService $docService
    ) {}

    public function issueForOrder(Order $order): void
    {
        if (! config('commerce-checkout.inventory.enabled', true)) {
            return;
        }

        $meta = Arr::wrap($order->meta);
        if (! empty($meta['inventory_doc_ids'])) {
            return;
        }

        $order->loadMissing(['items.product', 'items.variant']);

        $linesByItem = [];

        foreach ($order->items as $item) {
            $product = $item->product;
            if (! $product || ! $product->track_inventory) {
                continue;
            }

            $inventoryItemId = $item->variant?->inventory_item_id ?? $product->inventory_item_id;
            if (! $inventoryItemId) {
                continue;
            }

            $linesByItem[$inventoryItemId]['quantity'] = ($linesByItem[$inventoryItemId]['quantity'] ?? 0) + (float) $item->quantity;
            $linesByItem[$inventoryItemId]['meta'][] = [
                'order_item_id' => $item->getKey(),
                'product_id' => $item->product_id,
                'variant_id' => $item->variant_id,
                'sku' => $item->sku,
            ];
        }

        if ($linesByItem === []) {
            return;
        }

        $this->db->transaction(function () use ($order, $linesByItem, $meta): void {
            $itemIds = array_keys($linesByItem);

            $inventoryItems = InventoryItem::query()
                ->where('tenant_id', $order->tenant_id)
                ->whereIn('id', $itemIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($inventoryItems->count() !== count($itemIds)) {
                throw ValidationException::withMessages([
                    'inventory' => 'برخی اقلام انبار یافت نشدند.',
                ]);
            }

            $groups = [];
            foreach ($linesByItem as $itemId => $payload) {
                $inventoryItem = $inventoryItems->get($itemId);
                if (! $inventoryItem) {
                    continue;
                }

                $groups[$inventoryItem->company_id][] = [
                    'item' => $inventoryItem,
                    'quantity' => (float) $payload['quantity'],
                    'meta' => $payload['meta'] ?? [],
                ];
            }

            $docIds = [];

            foreach ($groups as $companyId => $lines) {
                $warehouseId = $this->resolveWarehouseId((int) $companyId);

                $doc = InventoryDoc::query()->create([
                    'tenant_id' => $order->tenant_id,
                    'company_id' => $companyId,
                    'warehouse_id' => $warehouseId,
                    'doc_type' => 'issue',
                    'doc_date' => now()->toDateString(),
                    'status' => 'draft',
                    'description' => 'خروج موجودی سفارش '.$order->number,
                    'metadata' => [
                        'order_id' => $order->getKey(),
                        'order_number' => $order->number,
                    ],
                ]);

                foreach ($lines as $line) {
                    $doc->lines()->create([
                        'inventory_item_id' => $line['item']->getKey(),
                        'quantity' => $line['quantity'],
                        'unit_cost' => null,
                        'metadata' => [
                            'order_id' => $order->getKey(),
                            'order_item_ids' => array_values(array_filter(array_column($line['meta'], 'order_item_id'))),
                        ],
                    ]);
                }

                $this->docService->post($doc);
                $docIds[] = $doc->getKey();
            }

            $order->update([
                'meta' => array_merge($meta, ['inventory_doc_ids' => $docIds]),
            ]);
        });
    }

    protected function resolveWarehouseId(int $companyId): ?int
    {
        $defaultWarehouseId = config('commerce-checkout.inventory.default_warehouse_id');

        if ($defaultWarehouseId) {
            $exists = InventoryWarehouse::query()
                ->where('company_id', $companyId)
                ->where('id', $defaultWarehouseId)
                ->exists();

            if ($exists) {
                return (int) $defaultWarehouseId;
            }
        }

        return InventoryWarehouse::query()
            ->where('company_id', $companyId)
            ->where('is_active', true)
            ->orderBy('id')
            ->value('id');
    }
}
