<?php

namespace Haida\Blog\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogPostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->getKey(),
            'tenant_id' => $this->tenant_id,
            'site_id' => $this->site_id,
            'category_id' => $this->category_id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
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
            'category' => $this->whenLoaded('category', function () {
                if (! $this->category) {
                    return null;
                }

                return [
                    'id' => $this->category->getKey(),
                    'name' => $this->category->name,
                    'slug' => $this->category->slug,
                ];
            }),
            'tags' => $this->whenLoaded('tags', function () {
                return $this->tags->map(fn ($tag) => [
                    'id' => $tag->getKey(),
                    'name' => $tag->name,
                    'slug' => $tag->slug,
                ])->all();
            }),
        ];
    }
}
