<?php

namespace Haida\FilamentRestaurantOps\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getKey(),
            'tenant_id' => $this->tenant_id,
            'company_id' => $this->company_id,
            'branch_id' => $this->branch_id,
            'supplier_id' => $this->supplier_id,
            'purchase_request_id' => $this->purchase_request_id,
            'order_no' => $this->order_no,
            'order_date' => optional($this->order_date)->toDateString(),
            'expected_at' => optional($this->expected_at)->toDateString(),
            'status' => $this->status,
            'subtotal' => $this->subtotal,
            'tax_total' => $this->tax_total,
            'discount_total' => $this->discount_total,
            'total' => $this->total,
            'notes' => $this->notes,
            'metadata' => $this->metadata,
            'lines' => $this->whenLoaded('lines', function () {
                return $this->lines->map(function ($line) {
                    return [
                        'id' => $line->getKey(),
                        'item_id' => $line->item_id,
                        'uom_id' => $line->uom_id,
                        'quantity' => $line->quantity,
                        'unit_price' => $line->unit_price,
                        'tax_rate' => $line->tax_rate,
                        'tax_amount' => $line->tax_amount,
                        'discount_amount' => $line->discount_amount,
                        'line_total' => $line->line_total,
                    ];
                })->values();
            }),
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
