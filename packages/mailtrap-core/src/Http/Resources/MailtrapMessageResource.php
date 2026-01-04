<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MailtrapMessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $includeBody = $request->boolean('include_body');

        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'connection_id' => $this->connection_id,
            'inbox_id' => $this->inbox_id,
            'message_id' => $this->message_id,
            'subject' => $this->subject,
            'from_email' => $this->from_email,
            'to_email' => $this->to_email,
            'sent_at' => optional($this->sent_at)->toIso8601String(),
            'size' => $this->size,
            'is_read' => $this->is_read,
            'attachments_count' => $this->attachments_count,
            'html_body' => $this->when($includeBody, $this->html_body),
            'text_body' => $this->when($includeBody, $this->text_body),
            'metadata' => $this->metadata,
            'synced_at' => optional($this->synced_at)->toIso8601String(),
        ];
    }
}
