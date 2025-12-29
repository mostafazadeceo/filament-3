<?php

namespace Vendor\FilamentAccountingIr\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PartyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'company_id' => $this->company_id,
            'party_type' => $this->party_type,
            'name' => $this->name,
            'legal_name' => $this->legal_name,
            'national_id' => $this->national_id,
            'economic_code' => $this->economic_code,
            'registration_number' => $this->registration_number,
            'phone' => $this->phone,
            'email' => $this->email,
            'is_active' => (bool) $this->is_active,
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}
