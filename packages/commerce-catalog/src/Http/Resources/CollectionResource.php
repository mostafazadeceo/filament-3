<?php

namespace Haida\CommerceCatalog\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CollectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->getKey(),
            'tenant_id' => $this->tenant_id,
            'site_id' => $this->site_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'status' => $this->status,
            'description' => $this->description,
            'published_at' => optional($this->published_at)->toISOString(),
            'products' => $this->whenLoaded('products', function () {
                return $this->products->map(fn ($product) => [
                    'id' => $product->getKey(),
                    'name' => $product->name,
                    'slug' => $product->slug,
                ])->all();
            }),
        ];
    }
}
