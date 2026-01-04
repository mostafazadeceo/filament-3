<?php

namespace Haida\CommerceOrders\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->getKey(),
            'product_id' => $this->resource->product_id,
            'variant_id' => $this->resource->variant_id,
            'name' => $this->resource->name,
            'sku' => $this->resource->sku,
            'quantity' => (float) $this->resource->quantity,
            'currency' => $this->resource->currency,
            'unit_price' => (float) $this->resource->unit_price,
            'line_total' => (float) $this->resource->line_total,
            'meta' => $this->resource->meta,
        ];
    }
}
