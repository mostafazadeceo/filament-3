<?php

namespace Haida\CommerceOrders\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->getKey(),
            'number' => $this->resource->number,
            'status' => $this->resource->status,
            'payment_status' => $this->resource->payment_status,
            'currency' => $this->resource->currency,
            'subtotal' => (float) $this->resource->subtotal,
            'discount_total' => (float) $this->resource->discount_total,
            'tax_total' => (float) $this->resource->tax_total,
            'shipping_total' => (float) $this->resource->shipping_total,
            'total' => (float) $this->resource->total,
            'customer_name' => $this->resource->customer_name,
            'customer_email' => $this->resource->customer_email,
            'customer_phone' => $this->resource->customer_phone,
            'billing_address' => $this->resource->billing_address,
            'shipping_address' => $this->resource->shipping_address,
            'customer_note' => $this->resource->customer_note,
            'internal_note' => $this->resource->internal_note,
            'placed_at' => optional($this->resource->placed_at)->toISOString(),
            'paid_at' => optional($this->resource->paid_at)->toISOString(),
            'created_at' => optional($this->resource->created_at)->toISOString(),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'payments' => OrderPaymentResource::collection($this->whenLoaded('payments')),
        ];
    }
}
