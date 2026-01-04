<?php

namespace Haida\CommerceCatalog\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->getKey(),
            'tenant_id' => $this->tenant_id,
            'site_id' => $this->site_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'type' => $this->type,
            'status' => $this->status,
            'sku' => $this->sku,
            'summary' => $this->summary,
            'description' => $this->description,
            'currency' => $this->currency,
            'price' => $this->price,
            'compare_at_price' => $this->compare_at_price,
            'track_inventory' => $this->track_inventory,
            'accounting_product_id' => $this->accounting_product_id,
            'inventory_item_id' => $this->inventory_item_id,
            'metadata' => $this->metadata,
            'published_at' => optional($this->published_at)->toISOString(),
            'collections' => $this->whenLoaded('collections', function () {
                return $this->collections->map(fn ($collection) => [
                    'id' => $collection->getKey(),
                    'name' => $collection->name,
                    'slug' => $collection->slug,
                ])->all();
            }),
        ];
    }
}
