<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MailtrapOfferResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'status' => $this->status,
            'description' => $this->description,
            'duration_days' => $this->duration_days,
            'feature_keys' => $this->feature_keys,
            'limits' => $this->limits,
            'price' => $this->price,
            'currency' => $this->currency,
            'catalog_product_id' => $this->catalog_product_id,
            'metadata' => $this->metadata,
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
