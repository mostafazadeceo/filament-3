<?php

namespace Haida\FilamentWorkhub\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttachmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getKey(),
            'tenant_id' => $this->tenant_id,
            'work_item_id' => $this->work_item_id,
            'user_id' => $this->user_id,
            'disk' => $this->disk,
            'path' => $this->path,
            'filename' => $this->filename,
            'mime_type' => $this->mime_type,
            'size' => $this->size,
            'created_at' => optional($this->created_at)->toIso8601String(),
        ];
    }
}
