<?php

namespace Haida\FilamentPettyCashIr\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseAttachmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'company_id' => $this->company_id,
            'expense_id' => $this->expense_id,
            'uploaded_by' => $this->uploaded_by,
            'path' => $this->path,
            'original_name' => $this->original_name,
            'mime_type' => $this->mime_type,
            'size' => $this->size,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at,
        ];
    }
}
