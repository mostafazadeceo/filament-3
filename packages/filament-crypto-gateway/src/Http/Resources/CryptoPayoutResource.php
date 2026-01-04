<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CryptoPayoutResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'provider' => $this->provider,
            'order_id' => $this->order_id,
            'external_uuid' => $this->external_uuid,
            'to_address' => $this->to_address,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'network' => $this->network,
            'fee' => $this->fee,
            'status' => (string) $this->status,
            'is_final' => $this->is_final,
            'approved_at' => $this->approved_at,
            'approved_by' => $this->approved_by,
            'approval_note' => $this->approval_note,
            'fail_reason' => $this->fail_reason,
            'txid' => $this->txid,
            'meta' => $this->meta,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
