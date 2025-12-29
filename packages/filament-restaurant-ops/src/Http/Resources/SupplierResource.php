<?php

namespace Haida\FilamentRestaurantOps\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getKey(),
            'tenant_id' => $this->tenant_id,
            'company_id' => $this->company_id,
            'accounting_party_id' => $this->accounting_party_id,
            'name' => $this->name,
            'code' => $this->code,
            'status' => $this->status,
            'contact_name' => $this->contact_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'payment_terms' => $this->payment_terms,
            'rating' => $this->rating,
            'metadata' => $this->metadata,
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
