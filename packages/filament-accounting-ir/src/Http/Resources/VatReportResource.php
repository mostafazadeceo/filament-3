<?php

namespace Vendor\FilamentAccountingIr\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VatReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'vat_period_id' => $this->vat_period_id,
            'sales_base' => $this->sales_base,
            'sales_tax' => $this->sales_tax,
            'purchase_base' => $this->purchase_base,
            'purchase_tax' => $this->purchase_tax,
            'status' => $this->status,
            'metadata' => $this->metadata,
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}
