<?php

namespace Vendor\FilamentAccountingIr\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PayrollRunResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'company_id' => $this->company_id,
            'fiscal_period_id' => $this->fiscal_period_id,
            'run_date' => optional($this->run_date)->toDateString(),
            'status' => $this->status,
            'metadata' => $this->metadata,
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}
