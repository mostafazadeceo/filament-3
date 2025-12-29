<?php

namespace Haida\FilamentRestaurantOps\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryDocResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getKey(),
            'tenant_id' => $this->tenant_id,
            'company_id' => $this->company_id,
            'accounting_inventory_doc_id' => $this->accounting_inventory_doc_id,
            'branch_id' => $this->branch_id,
            'warehouse_id' => $this->warehouse_id,
            'doc_no' => $this->doc_no,
            'doc_type' => $this->doc_type,
            'status' => $this->status,
            'doc_date' => optional($this->doc_date)->toDateString(),
            'reference_type' => $this->reference_type,
            'reference_id' => $this->reference_id,
            'notes' => $this->notes,
            'metadata' => $this->metadata,
            'lines' => $this->whenLoaded('lines', function () {
                return $this->lines->map(function ($line) {
                    return [
                        'id' => $line->getKey(),
                        'item_id' => $line->item_id,
                        'uom_id' => $line->uom_id,
                        'quantity' => $line->quantity,
                        'unit_cost' => $line->unit_cost,
                        'batch_no' => $line->batch_no,
                        'expires_at' => optional($line->expires_at)->toDateString(),
                        'metadata' => $line->metadata,
                    ];
                })->values();
            }),
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
