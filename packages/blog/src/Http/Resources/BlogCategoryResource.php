<?php

namespace Haida\Blog\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->getKey(),
            'tenant_id' => $this->tenant_id,
            'site_id' => $this->site_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'site' => $this->whenLoaded('site', function () {
                return [
                    'id' => $this->site->getKey(),
                    'name' => $this->site->name,
                    'slug' => $this->site->slug,
                ];
            }),
        ];
    }
}
