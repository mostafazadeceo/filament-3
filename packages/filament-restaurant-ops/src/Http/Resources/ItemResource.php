<?php

namespace Haida\FilamentRestaurantOps\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getKey(),
            'tenant_id' => $this->tenant_id,
            'company_id' => $this->company_id,
            'accounting_inventory_item_id' => $this->accounting_inventory_item_id,
            'name' => $this->name,
            'code' => $this->code,
            'category' => $this->category,
            'is_active' => $this->is_active,
            'base_uom_id' => $this->base_uom_id,
            'purchase_uom_id' => $this->purchase_uom_id,
            'consumption_uom_id' => $this->consumption_uom_id,
            'purchase_to_base_rate' => $this->purchase_to_base_rate,
            'consumption_to_base_rate' => $this->consumption_to_base_rate,
            'min_stock' => $this->min_stock,
            'max_stock' => $this->max_stock,
            'reorder_point' => $this->reorder_point,
            'track_batch' => $this->track_batch,
            'track_expiry' => $this->track_expiry,
            'metadata' => $this->metadata,
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
