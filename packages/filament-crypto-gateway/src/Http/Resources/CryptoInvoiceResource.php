<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CryptoInvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'provider' => $this->provider,
            'order_id' => $this->order_id,
            'external_uuid' => $this->external_uuid,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'to_currency' => $this->to_currency,
            'network' => $this->network,
            'address' => $this->address,
            'status' => (string) $this->status,
            'is_final' => $this->is_final,
            'expires_at' => $this->expires_at,
            'tolerance_percent' => $this->tolerance_percent,
            'subtract_percent' => $this->subtract_percent,
            'meta' => $this->meta,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
