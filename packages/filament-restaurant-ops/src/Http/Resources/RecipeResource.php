<?php

namespace Haida\FilamentRestaurantOps\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getKey(),
            'tenant_id' => $this->tenant_id,
            'company_id' => $this->company_id,
            'name' => $this->name,
            'code' => $this->code,
            'yield_quantity' => $this->yield_quantity,
            'yield_uom_id' => $this->yield_uom_id,
            'waste_percent' => $this->waste_percent,
            'is_active' => $this->is_active,
            'metadata' => $this->metadata,
            'lines' => $this->whenLoaded('lines', function () {
                return $this->lines->map(function ($line) {
                    return [
                        'id' => $line->getKey(),
                        'item_id' => $line->item_id,
                        'uom_id' => $line->uom_id,
                        'quantity' => $line->quantity,
                        'waste_percent' => $line->waste_percent,
                        'is_optional' => $line->is_optional,
                    ];
                })->values();
            }),
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
