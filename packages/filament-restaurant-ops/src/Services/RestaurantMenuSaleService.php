<?php

namespace Haida\FilamentRestaurantOps\Services;

use Haida\FilamentRestaurantOps\Models\RestaurantInventoryDoc;
use Haida\FilamentRestaurantOps\Models\RestaurantInventoryLot;
use Haida\FilamentRestaurantOps\Models\RestaurantItem;
use Haida\FilamentRestaurantOps\Models\RestaurantMenuSale;
use Haida\FilamentRestaurantOps\Models\RestaurantStockMove;
use Haida\FilamentRestaurantOps\Models\RestaurantWarehouse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RestaurantMenuSaleService
{
    public function post(RestaurantMenuSale $sale): RestaurantInventoryDoc
    {
        if ($sale->status === 'posted') {
            $existing = RestaurantInventoryDoc::query()
                ->where('reference_type', $this->referenceType())
                ->where('reference_id', $sale->getKey())
                ->first();

            if ($existing) {
                return $existing;
            }
        }

        return DB::transaction(function () use ($sale): RestaurantInventoryDoc {
            $sale->loadMissing('lines.menuItem.recipe.lines.item');

            if ($sale->lines->isEmpty()) {
                throw ValidationException::withMessages([
                    'lines' => 'برای فروش منو آیتمی ثبت نشده است.',
                ]);
            }

            $warehouseId = $sale->warehouse_id ?: $this->resolveWarehouseId($sale);

            if (! $warehouseId) {
                throw ValidationException::withMessages([
                    'warehouse_id' => 'انبار مصرف مشخص نشده است.',
                ]);
            }

            $doc = RestaurantInventoryDoc::query()->firstOrCreate(
                [
                    'reference_type' => $this->referenceType(),
                    'reference_id' => $sale->getKey(),
                ],
                [
                    'tenant_id' => $sale->tenant_id,
                    'company_id' => $sale->company_id,
                    'branch_id' => $sale->branch_id,
                    'warehouse_id' => $warehouseId,
                    'doc_no' => $sale->external_ref,
                    'doc_type' => 'consumption',
                    'status' => 'draft',
                    'doc_date' => $sale->sale_date ?? now(),
                    'notes' => 'مصرف بر اساس فروش منو',
                ]
            );

            if ($doc->status !== 'posted') {
                $itemsById = [];
                $lines = [];

                foreach ($sale->lines as $saleLine) {
                    $menuItem = $saleLine->menuItem;
                    $recipe = $menuItem?->recipe;

                    if (! $recipe) {
                        throw ValidationException::withMessages([
                            'recipe_id' => 'برای آیتم منو، فرمول تولید تعریف نشده است.',
                        ]);
                    }

                    foreach ($recipe->lines as $recipeLine) {
                        $qty = (float) $saleLine->quantity * (float) $recipeLine->quantity;
                        $wastePercent = (float) $recipe->waste_percent + (float) $recipeLine->waste_percent;
                        if ($wastePercent > 0) {
                            $qty *= (1 + ($wastePercent / 100));
                        }

                        $key = $recipeLine->item_id.'-'.$recipeLine->uom_id;
                        if (! isset($lines[$key])) {
                            $lines[$key] = [
                                'item_id' => $recipeLine->item_id,
                                'uom_id' => $recipeLine->uom_id,
                                'quantity' => 0,
                            ];
                        }

                        $lines[$key]['quantity'] += $qty;

                        if ($recipeLine->item) {
                            $itemsById[$recipeLine->item_id] = $recipeLine->item;
                        }
                    }
                }

                $lines = $this->expandLinesWithLots($warehouseId, array_values($lines), $itemsById);
                $itemIds = array_values(array_unique(array_column($lines, 'item_id')));
                $latestCosts = $this->latestCosts($warehouseId, $itemIds);

                $doc->lines()->delete();
                foreach ($lines as $line) {
                    $doc->lines()->create([
                        'item_id' => $line['item_id'],
                        'uom_id' => $line['uom_id'],
                        'quantity' => $line['quantity'],
                        'unit_cost' => $latestCosts[$line['item_id']] ?? 0,
                        'batch_no' => $line['batch_no'] ?? null,
                        'expires_at' => $line['expires_at'] ?? null,
                        'metadata' => null,
                    ]);
                }

                $doc = app(RestaurantInventoryDocService::class)->post($doc);
            }

            $sale->update([
                'status' => 'posted',
                'warehouse_id' => $warehouseId,
                'sale_date' => $sale->sale_date ?? now(),
                'total_amount' => $sale->total_amount ?: $sale->lines->sum('line_total'),
            ]);

            return $doc->refresh();
        });
    }

    protected function latestCosts(int $warehouseId, array $itemIds): array
    {
        if ($itemIds === []) {
            return [];
        }

        $moves = RestaurantStockMove::query()
            ->where('warehouse_id', $warehouseId)
            ->whereIn('item_id', $itemIds)
            ->where('direction', 'in')
            ->orderByDesc('move_date')
            ->orderByDesc('id')
            ->get()
            ->groupBy('item_id');

        return $moves->map(fn ($rows) => (float) $rows->first()->unit_cost)->toArray();
    }

    /**
     * @param  array<int, array<string, mixed>>  $lines
     * @param  array<int, RestaurantItem>  $itemsById
     * @return array<int, array<string, mixed>>
     */
    protected function expandLinesWithLots(int $warehouseId, array $lines, array $itemsById): array
    {
        $expanded = [];
        $allowNegative = (bool) config('filament-restaurant-ops.inventory.allow_negative', false);

        foreach ($lines as $line) {
            $item = $itemsById[$line['item_id']] ?? null;
            if (! $item instanceof RestaurantItem) {
                $item = RestaurantItem::query()->find($line['item_id']);
                if ($item) {
                    $itemsById[$item->getKey()] = $item;
                }
            }

            $requiresLot = $item && ($item->track_batch || $item->track_expiry);
            if (! $requiresLot || ! empty($line['batch_no']) || ! empty($line['expires_at'])) {
                $expanded[] = $line;

                continue;
            }

            $remainingBase = $this->normalizeQuantity((float) $line['quantity'], $line['uom_id'] ?? null, $item);

            $lots = RestaurantInventoryLot::query()
                ->where('warehouse_id', $warehouseId)
                ->where('item_id', $line['item_id'])
                ->where('quantity', '>', 0)
                ->orderByRaw('expires_at is null')
                ->orderBy('expires_at')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            foreach ($lots as $lot) {
                if ($remainingBase <= 0) {
                    break;
                }

                $takeBase = min($remainingBase, (float) $lot->quantity);
                $expanded[] = [
                    'item_id' => $line['item_id'],
                    'uom_id' => $line['uom_id'],
                    'quantity' => $this->denormalizeQuantity($takeBase, $line['uom_id'] ?? null, $item),
                    'batch_no' => $lot->batch_no,
                    'expires_at' => $lot->expires_at,
                ];

                $remainingBase -= $takeBase;
            }

            if ($remainingBase > 0 && $allowNegative && $lots->isNotEmpty()) {
                $lastLot = $lots->last();
                $expanded[] = [
                    'item_id' => $line['item_id'],
                    'uom_id' => $line['uom_id'],
                    'quantity' => $this->denormalizeQuantity($remainingBase, $line['uom_id'] ?? null, $item),
                    'batch_no' => $lastLot->batch_no,
                    'expires_at' => $lastLot->expires_at,
                ];
                $remainingBase = 0;
            }

            if ($remainingBase > 0) {
                throw ValidationException::withMessages([
                    'quantity' => 'موجودی بچ کافی نیست.',
                ]);
            }
        }

        return $expanded;
    }

    protected function normalizeQuantity(float $quantity, ?int $uomId, ?RestaurantItem $item): float
    {
        if (! $item || ! $uomId || ! $item->base_uom_id) {
            return $quantity;
        }

        if ($uomId === $item->purchase_uom_id && $item->purchase_to_base_rate) {
            return $quantity * (float) $item->purchase_to_base_rate;
        }

        if ($uomId === $item->consumption_uom_id && $item->consumption_to_base_rate) {
            return $quantity * (float) $item->consumption_to_base_rate;
        }

        return $quantity;
    }

    protected function denormalizeQuantity(float $quantity, ?int $uomId, ?RestaurantItem $item): float
    {
        if (! $item || ! $uomId || ! $item->base_uom_id) {
            return $quantity;
        }

        if ($uomId === $item->purchase_uom_id && $item->purchase_to_base_rate) {
            return $quantity / (float) $item->purchase_to_base_rate;
        }

        if ($uomId === $item->consumption_uom_id && $item->consumption_to_base_rate) {
            return $quantity / (float) $item->consumption_to_base_rate;
        }

        return $quantity;
    }

    protected function resolveWarehouseId(RestaurantMenuSale $sale): ?int
    {
        $query = RestaurantWarehouse::query()
            ->where('company_id', $sale->company_id)
            ->where('is_active', true);

        if ($sale->branch_id) {
            $query->where('branch_id', $sale->branch_id);
        }

        return $query->value('id');
    }

    protected function referenceType(): string
    {
        return 'restaurant_menu_sales';
    }
}
