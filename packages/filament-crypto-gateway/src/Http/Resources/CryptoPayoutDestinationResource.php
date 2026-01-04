<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CryptoPayoutDestinationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'label' => $this->label,
            'address' => $this->address,
            'currency' => $this->currency,
            'network' => $this->network,
            'status' => $this->status,
            'approved_at' => $this->approved_at,
            'approved_by' => $this->approved_by,
            'last_used_at' => $this->last_used_at,
            'meta' => $this->meta,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
