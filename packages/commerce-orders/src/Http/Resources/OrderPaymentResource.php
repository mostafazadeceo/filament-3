<?php

namespace Haida\CommerceOrders\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderPaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->getKey(),
            'method' => $this->resource->method,
            'status' => $this->resource->status,
            'currency' => $this->resource->currency,
            'amount' => (float) $this->resource->amount,
            'provider' => $this->resource->provider,
            'reference' => $this->resource->reference,
            'wallet_transaction_id' => $this->resource->wallet_transaction_id,
            'wallet_hold_id' => $this->resource->wallet_hold_id,
            'meta' => $this->resource->meta,
            'created_at' => optional($this->resource->created_at)->toISOString(),
        ];
    }
}
