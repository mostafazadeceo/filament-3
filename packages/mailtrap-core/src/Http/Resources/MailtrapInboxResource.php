<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MailtrapInboxResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'connection_id' => $this->connection_id,
            'inbox_id' => $this->inbox_id,
            'name' => $this->name,
            'status' => $this->status,
            'username' => $this->username,
            'email_domain' => $this->email_domain,
            'api_domain' => $this->api_domain,
            'smtp_ports' => $this->smtp_ports,
            'messages_count' => $this->messages_count,
            'unread_count' => $this->unread_count,
            'last_message_sent_at' => optional($this->last_message_sent_at)->toIso8601String(),
            'synced_at' => optional($this->synced_at)->toIso8601String(),
            'metadata' => $this->metadata,
        ];
    }
}
