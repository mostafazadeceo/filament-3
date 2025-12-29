<?php

namespace Haida\FilamentRestaurantOps\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getKey(),
            'tenant_id' => $this->tenant_id,
            'company_id' => $this->company_id,
            'branch_id' => $this->branch_id,
            'requested_by' => $this->requested_by,
            'status' => $this->status,
            'needed_at' => optional($this->needed_at)->toDateString(),
            'notes' => $this->notes,
            'lines' => $this->whenLoaded('lines', function () {
                return $this->lines->map(function ($line) {
                    return [
                        'id' => $line->getKey(),
                        'item_id' => $line->item_id,
                        'uom_id' => $line->uom_id,
                        'quantity' => $line->quantity,
                        'notes' => $line->notes,
                    ];
                })->values();
            }),
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
