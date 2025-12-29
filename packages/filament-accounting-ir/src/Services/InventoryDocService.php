<?php

namespace Vendor\FilamentAccountingIr\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Vendor\FilamentAccountingIr\Events\InventoryDocPosted;
use Vendor\FilamentAccountingIr\Models\AccountingCompanySetting;
use Vendor\FilamentAccountingIr\Models\InventoryDoc;
use Vendor\FilamentAccountingIr\Models\StockMove;

class InventoryDocService
{
    public function post(InventoryDoc $doc): InventoryDoc
    {
        $doc->loadMissing(['lines.item']);

        if ($doc->lines->isEmpty()) {
            throw ValidationException::withMessages([
                'lines' => 'حداقل یک ردیف برای سند انبار لازم است.',
            ]);
        }

        $direction = $this->resolveDirection($doc->doc_type);
        $hasMoves = StockMove::query()
            ->where('inventory_doc_id', $doc->getKey())
            ->exists();

        if ($doc->status === 'posted' && $hasMoves) {
            return $doc;
        }

        $allowNegative = $this->resolveAllowNegativeInventory($doc->company_id);

        return DB::transaction(function () use ($doc, $direction, $allowNegative): InventoryDoc {
            $doc->update([
                'status' => 'posted',
            ]);

            foreach ($doc->lines as $line) {
                $item = $line->item;
                if (! $item) {
                    continue;
                }

                $quantity = (float) $line->quantity;
                if ($quantity <= 0) {
                    continue;
                }

                $signedQuantity = $direction === 'out' ? -$quantity : $quantity;
                $newStock = (float) $item->current_stock + $signedQuantity;

                if ($newStock < 0 && ! ($item->allow_negative || $allowNegative)) {
                    throw ValidationException::withMessages([
                        'quantity' => 'موجودی کافی نیست و منفی شدن مجاز نیست.',
                    ]);
                }

                $item->update([
                    'current_stock' => $newStock,
                ]);

                StockMove::query()->create([
                    'tenant_id' => $doc->tenant_id,
                    'company_id' => $doc->company_id,
                    'inventory_item_id' => $item->getKey(),
                    'inventory_doc_id' => $doc->getKey(),
                    'quantity' => $quantity,
                    'unit_cost' => $line->unit_cost,
                    'direction' => $direction,
                    'move_date' => $doc->doc_date,
                    'metadata' => $line->metadata,
                ]);
            }

            $doc->refresh();
            event(new InventoryDocPosted($doc));

            return $doc;
        });
    }

    protected function resolveAllowNegativeInventory(int $companyId): bool
    {
        $setting = AccountingCompanySetting::query()
            ->where('company_id', $companyId)
            ->value('allow_negative_inventory');

        if ($setting !== null) {
            return (bool) $setting;
        }

        return (bool) config('filament-accounting-ir.ledger.allow_negative_inventory', false);
    }

    protected function resolveDirection(string $docType): string
    {
        return match ($docType) {
            'issue', 'transfer' => 'out',
            default => 'in',
        };
    }
}
