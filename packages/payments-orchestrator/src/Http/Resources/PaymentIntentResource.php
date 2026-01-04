<?php

namespace Haida\PaymentsOrchestrator\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentIntentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->getKey(),
            'order_id' => $this->resource->order_id,
            'provider_key' => $this->resource->provider_key,
            'status' => $this->resource->status,
            'currency' => $this->resource->currency,
            'amount' => (float) $this->resource->amount,
            'idempotency_key' => $this->resource->idempotency_key,
            'provider_reference' => $this->resource->provider_reference,
            'redirect_url' => $this->resource->redirect_url,
            'created_at' => optional($this->resource->created_at)->toISOString(),
        ];
    }
}
