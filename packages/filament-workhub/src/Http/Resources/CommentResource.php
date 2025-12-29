<?php

namespace Haida\FilamentWorkhub\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getKey(),
            'tenant_id' => $this->tenant_id,
            'work_item_id' => $this->work_item_id,
            'user_id' => $this->user_id,
            'body' => $this->body,
            'is_internal' => (bool) $this->is_internal,
            'created_at' => optional($this->created_at)->toIso8601String(),
        ];
    }
}
