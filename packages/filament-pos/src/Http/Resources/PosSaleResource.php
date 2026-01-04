<?php

namespace Haida\FilamentPos\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PosSaleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'store_id' => $this->store_id,
            'register_id' => $this->register_id,
            'session_id' => $this->session_id,
            'device_id' => $this->device_id,
            'receipt_no' => $this->receipt_no,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'currency' => $this->currency,
            'subtotal' => $this->subtotal,
            'discount_total' => $this->discount_total,
            'tax_total' => $this->tax_total,
            'total' => $this->total,
            'source' => $this->source,
            'idempotency_key' => $this->idempotency_key,
            'created_by_user_id' => $this->created_by_user_id,
            'completed_at' => $this->completed_at,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
