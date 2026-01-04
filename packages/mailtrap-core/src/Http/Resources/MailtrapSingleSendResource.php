<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MailtrapSingleSendResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'connection_id' => $this->connection_id,
            'to_email' => $this->to_email,
            'to_name' => $this->to_name,
            'subject' => $this->subject,
            'status' => $this->status,
            'error_message' => $this->error_message,
            'sent_at' => optional($this->sent_at)->toIso8601String(),
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
