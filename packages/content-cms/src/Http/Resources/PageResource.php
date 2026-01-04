<?php

namespace Haida\ContentCms\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->getKey(),
            'tenant_id' => $this->tenant_id,
            'site_id' => $this->site_id,
            'slug' => $this->slug,
            'title' => $this->title,
            'status' => $this->status,
            'seo' => $this->seo,
            'draft_content' => $this->draft_content,
            'published_content' => $this->published_content,
            'published_at' => optional($this->published_at)->toISOString(),
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
