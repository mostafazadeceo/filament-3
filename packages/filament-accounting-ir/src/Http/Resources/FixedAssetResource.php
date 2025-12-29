<?php

namespace Vendor\FilamentAccountingIr\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FixedAssetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'company_id' => $this->company_id,
            'branch_id' => $this->branch_id,
            'name' => $this->name,
            'asset_code' => $this->asset_code,
            'category' => $this->category,
            'acquisition_date' => optional($this->acquisition_date)->toDateString(),
            'cost' => (float) $this->cost,
            'salvage_value' => (float) $this->salvage_value,
            'depreciation_method' => $this->depreciation_method,
            'useful_life_months' => $this->useful_life_months,
            'status' => $this->status,
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}
