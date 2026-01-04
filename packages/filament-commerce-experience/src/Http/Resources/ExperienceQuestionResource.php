<?php

namespace Haida\FilamentCommerceExperience\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExperienceQuestionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'product_id' => $this->product_id,
            'customer_id' => $this->customer_id,
            'question' => $this->question,
            'status' => $this->status,
            'answered_at' => $this->answered_at,
            'metadata' => $this->metadata,
        ];
    }
}
