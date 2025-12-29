<?php

namespace Vendor\FilamentAccountingIr\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountingCompanyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'name' => $this->name,
            'legal_name' => $this->legal_name,
            'national_id' => $this->national_id,
            'economic_code' => $this->economic_code,
            'registration_number' => $this->registration_number,
            'vat_number' => $this->vat_number,
            'timezone' => $this->timezone,
            'base_currency' => $this->base_currency,
            'is_active' => (bool) $this->is_active,
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}
