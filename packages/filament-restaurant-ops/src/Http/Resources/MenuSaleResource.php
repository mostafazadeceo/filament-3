<?php

namespace Haida\FilamentRestaurantOps\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuSaleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getKey(),
            'tenant_id' => $this->tenant_id,
            'company_id' => $this->company_id,
            'accounting_journal_entry_id' => $this->accounting_journal_entry_id,
            'branch_id' => $this->branch_id,
            'warehouse_id' => $this->warehouse_id,
            'sale_date' => optional($this->sale_date)->toDateString(),
            'source' => $this->source,
            'external_ref' => $this->external_ref,
            'total_amount' => $this->total_amount,
            'status' => $this->status,
            'lines' => $this->whenLoaded('lines', function () {
                return $this->lines->map(function ($line) {
                    return [
                        'id' => $line->getKey(),
                        'menu_item_id' => $line->menu_item_id,
                        'quantity' => $line->quantity,
                        'unit_price' => $line->unit_price,
                        'line_total' => $line->line_total,
                    ];
                })->values();
            }),
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
