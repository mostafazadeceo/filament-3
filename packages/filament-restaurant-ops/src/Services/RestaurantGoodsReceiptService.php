<?php

namespace Haida\FilamentRestaurantOps\Services;

use Haida\FilamentRestaurantOps\Models\RestaurantGoodsReceipt;
use Haida\FilamentRestaurantOps\Models\RestaurantInventoryDoc;
use Haida\FilamentRestaurantOps\Models\RestaurantPurchaseOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RestaurantGoodsReceiptService
{
    public function post(RestaurantGoodsReceipt $receipt): RestaurantInventoryDoc
    {
        if ($receipt->status === 'posted') {
            $existing = RestaurantInventoryDoc::query()
                ->where('reference_type', $this->referenceType())
                ->where('reference_id', $receipt->getKey())
                ->first();

            if ($existing) {
                return $existing;
            }
        }

        return DB::transaction(function () use ($receipt): RestaurantInventoryDoc {
            $receipt->loadMissing('lines');

            if ($receipt->lines->isEmpty()) {
                throw ValidationException::withMessages([
                    'lines' => 'برای رسید کالا آیتمی ثبت نشده است.',
                ]);
            }

            $doc = RestaurantInventoryDoc::query()->firstOrCreate(
                [
                    'reference_type' => $this->referenceType(),
                    'reference_id' => $receipt->getKey(),
                ],
                [
                    'tenant_id' => $receipt->tenant_id,
                    'company_id' => $receipt->company_id,
                    'branch_id' => $receipt->branch_id,
                    'warehouse_id' => $receipt->warehouse_id,
                    'doc_no' => $receipt->receipt_no,
                    'doc_type' => 'receipt',
                    'status' => 'draft',
                    'doc_date' => $receipt->receipt_date ?? now(),
                    'notes' => $receipt->notes,
                ]
            );

            if ($doc->status !== 'posted') {
                $doc->lines()->delete();
                foreach ($receipt->lines as $line) {
                    $doc->lines()->create([
                        'item_id' => $line->item_id,
                        'uom_id' => $line->uom_id,
                        'quantity' => $line->quantity,
                        'unit_cost' => $line->unit_cost,
                        'batch_no' => $line->batch_no,
                        'expires_at' => $line->expires_at,
                        'metadata' => null,
                    ]);
                }

                $doc = app(RestaurantInventoryDocService::class)->post($doc);
            }

            $receipt->update([
                'status' => 'posted',
                'receipt_date' => $receipt->receipt_date ?? now(),
            ]);

            $this->syncPurchaseOrderStatus($receipt);

            return $doc->refresh();
        });
    }

    protected function syncPurchaseOrderStatus(RestaurantGoodsReceipt $receipt): void
    {
        if (! $receipt->purchase_order_id) {
            return;
        }

        $order = RestaurantPurchaseOrder::query()->with('lines')->find($receipt->purchase_order_id);
        if (! $order) {
            return;
        }

        $ordered = $order->lines->groupBy('item_id')->map(function ($lines) {
            return $lines->sum('quantity');
        });

        $receivedLines = $order->goodsReceipts()
            ->where('status', 'posted')
            ->with('lines')
            ->get()
            ->flatMap->lines
            ->groupBy('item_id')
            ->map(fn ($lines) => $lines->sum('quantity'));

        $receivedAny = false;
        $fullyReceived = true;

        foreach ($ordered as $itemId => $orderedQty) {
            $receivedQty = (float) ($receivedLines[$itemId] ?? 0);
            if ($receivedQty > 0) {
                $receivedAny = true;
            }

            if ($receivedQty < (float) $orderedQty) {
                $fullyReceived = false;
            }
        }

        if (! $receivedAny) {
            return;
        }

        $order->status = $fullyReceived ? 'received' : 'partially_received';
        $order->save();
    }

    protected function referenceType(): string
    {
        return 'restaurant_goods_receipts';
    }
}
