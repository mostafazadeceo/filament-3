<?php

namespace Haida\FilamentCommerceExperience\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExperienceReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'product_id' => $this->product_id,
            'order_id' => $this->order_id,
            'customer_id' => $this->customer_id,
            'rating' => $this->rating,
            'title' => $this->title,
            'body' => $this->body,
            'status' => $this->status,
            'verified_purchase' => $this->verified_purchase,
            'helpful_count' => $this->helpful_count,
            'abuse_flag' => $this->abuse_flag,
            'published_at' => $this->published_at,
            'metadata' => $this->metadata,
        ];
    }
}
