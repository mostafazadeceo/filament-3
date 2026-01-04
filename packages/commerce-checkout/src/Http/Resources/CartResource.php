<?php

namespace Haida\CommerceCheckout\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->getKey(),
            'public_id' => $this->resource->public_id,
            'site_id' => $this->resource->site_id,
            'user_id' => $this->resource->user_id,
            'status' => $this->resource->status,
            'currency' => $this->resource->currency,
            'subtotal' => (float) $this->resource->subtotal,
            'discount_total' => (float) $this->resource->discount_total,
            'tax_total' => (float) $this->resource->tax_total,
            'shipping_total' => (float) $this->resource->shipping_total,
            'total' => (float) $this->resource->total,
            'expires_at' => optional($this->resource->expires_at)->toISOString(),
            'items' => CartItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
