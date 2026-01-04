<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MailtrapConnectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'name' => $this->name,
            'status' => $this->status,
            'account_id' => $this->account_id,
            'default_inbox_id' => $this->default_inbox_id,
            'last_tested_at' => optional($this->last_tested_at)->toIso8601String(),
            'last_sync_at' => optional($this->last_sync_at)->toIso8601String(),
            'metadata' => $this->metadata,
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
