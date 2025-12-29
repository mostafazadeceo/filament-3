<?php

namespace Haida\FilamentRestaurantOps\Services;

use Carbon\Carbon;
use Haida\FilamentRestaurantOps\Models\RestaurantInventoryBalance;
use Haida\FilamentRestaurantOps\Models\RestaurantInventoryDoc;
use Haida\FilamentRestaurantOps\Models\RestaurantInventoryDocLine;
use Haida\FilamentRestaurantOps\Models\RestaurantInventoryLot;
use Haida\FilamentRestaurantOps\Models\RestaurantItem;
use Haida\FilamentRestaurantOps\Models\RestaurantStockMove;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RestaurantInventoryDocService
{
    private const IN_TYPES = ['receipt', 'transfer_in', 'adjustment_in'];

    private const OUT_TYPES = ['issue', 'transfer_out', 'adjustment_out', 'waste', 'consumption'];

    public function post(RestaurantInventoryDoc $doc): RestaurantInventoryDoc
    {
        if ($doc->status === 'posted') {
            return $doc;
        }

        return DB::transaction(function () use ($doc): RestaurantInventoryDoc {
            $doc->loadMissing('lines.item');

            if ($doc->lines->isEmpty()) {
                throw ValidationException::withMessages([
                    'lines' => 'برای سند انبار آیتمی ثبت نشده است.',
                ]);
            }

            $direction = $this->resolveDirection($doc->doc_type);
            $docDate = $doc->doc_date ?? now();

            foreach ($doc->lines as $line) {
                $this->applyLine($doc, $line, $direction, $docDate);
            }

            $doc->update([
                'status' => 'posted',
                'doc_date' => $docDate,
            ]);

            return $doc->refresh();
        });
    }

    protected function resolveDirection(?string $docType): string
    {
        if (in_array($docType, self::OUT_TYPES, true)) {
            return 'out';
        }

        return 'in';
    }

    protected function applyLine(
        RestaurantInventoryDoc $doc,
        RestaurantInventoryDocLine $line,
        string $direction,
        Carbon $docDate
    ): void {
        $item = $line->item;
        if (! $item instanceof RestaurantItem) {
            throw ValidationException::withMessages([
                'item_id' => 'کالا نامعتبر است.',
            ]);
        }

        $normalizedQty = $this->normalizeQuantity($line, $item);

        if ($normalizedQty <= 0) {
            throw ValidationException::withMessages([
                'quantity' => 'مقدار باید بزرگتر از صفر باشد.',
            ]);
        }

        $allowNegative = (bool) config('filament-restaurant-ops.inventory.allow_negative', false);
        $sign = $direction === 'in' ? 1 : -1;

        $balance = RestaurantInventoryBalance::query()
            ->where('warehouse_id', $doc->warehouse_id)
            ->where('item_id', $item->getKey())
            ->lockForUpdate()
            ->first();

        if (! $balance) {
            $balance = new RestaurantInventoryBalance([
                'tenant_id' => $doc->tenant_id,
                'company_id' => $doc->company_id,
                'warehouse_id' => $doc->warehouse_id,
                'item_id' => $item->getKey(),
                'quantity' => 0,
            ]);
        }

        $nextQty = (float) $balance->quantity + ($sign * $normalizedQty);

        if (! $allowNegative && $nextQty < 0) {
            throw ValidationException::withMessages([
                'quantity' => 'موجودی منفی مجاز نیست.',
            ]);
        }

        $balance->quantity = $nextQty;
        $balance->save();

        $this->applyLot($doc, $line, $item, $direction, $normalizedQty, $allowNegative);

        RestaurantStockMove::query()->create([
            'tenant_id' => $doc->tenant_id,
            'company_id' => $doc->company_id,
            'warehouse_id' => $doc->warehouse_id,
            'item_id' => $item->getKey(),
            'inventory_doc_id' => $doc->getKey(),
            'direction' => $direction,
            'quantity' => $normalizedQty,
            'unit_cost' => $line->unit_cost ?? 0,
            'move_date' => $docDate,
            'batch_no' => $line->batch_no,
            'expires_at' => $line->expires_at,
        ]);
    }

    protected function applyLot(
        RestaurantInventoryDoc $doc,
        RestaurantInventoryDocLine $line,
        RestaurantItem $item,
        string $direction,
        float $normalizedQty,
        bool $allowNegative
    ): void {
        $trackBatch = $item->track_batch || $item->track_expiry;
        if (! $trackBatch) {
            return;
        }

        if (! $line->batch_no && ! $line->expires_at) {
            throw ValidationException::withMessages([
                'batch_no' => 'برای کالاهای رهگیری‌شونده، بچ یا تاریخ انقضا الزامی است.',
            ]);
        }

        $lot = RestaurantInventoryLot::query()
            ->where('warehouse_id', $doc->warehouse_id)
            ->where('item_id', $item->getKey())
            ->where('batch_no', $line->batch_no)
            ->where('expires_at', $line->expires_at)
            ->lockForUpdate()
            ->first();

        if (! $lot) {
            $lot = new RestaurantInventoryLot([
                'tenant_id' => $doc->tenant_id,
                'company_id' => $doc->company_id,
                'warehouse_id' => $doc->warehouse_id,
                'item_id' => $item->getKey(),
                'batch_no' => $line->batch_no,
                'expires_at' => $line->expires_at,
                'quantity' => 0,
            ]);
        }

        $sign = $direction === 'in' ? 1 : -1;
        $nextQty = (float) $lot->quantity + ($sign * $normalizedQty);

        if (! $allowNegative && $nextQty < 0) {
            throw ValidationException::withMessages([
                'quantity' => 'موجودی بچ منفی مجاز نیست.',
            ]);
        }

        $lot->quantity = $nextQty;
        $lot->save();
    }

    protected function normalizeQuantity(RestaurantInventoryDocLine $line, RestaurantItem $item): float
    {
        $quantity = (float) $line->quantity;

        if (! $line->uom_id || ! $item->base_uom_id) {
            return $quantity;
        }

        if ($line->uom_id === $item->purchase_uom_id && $item->purchase_to_base_rate) {
            return $quantity * (float) $item->purchase_to_base_rate;
        }

        if ($line->uom_id === $item->consumption_uom_id && $item->consumption_to_base_rate) {
            return $quantity * (float) $item->consumption_to_base_rate;
        }

        return $quantity;
    }
}
