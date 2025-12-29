<?php

namespace Vendor\FilamentAccountingIr\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'company_id' => $this->company_id,
            'code' => $this->code,
            'name' => $this->name,
            'item_type' => $this->item_type,
            'uom_id' => $this->uom_id,
            'tax_category_id' => $this->tax_category_id,
            'base_price' => $this->base_price !== null ? (float) $this->base_price : null,
            'is_active' => (bool) $this->is_active,
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}
