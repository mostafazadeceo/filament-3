<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MailtrapCampaignResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'connection_id' => $this->connection_id,
            'audience_id' => $this->audience_id,
            'name' => $this->name,
            'subject' => $this->subject,
            'from_email' => $this->from_email,
            'from_name' => $this->from_name,
            'status' => $this->status,
            'scheduled_at' => optional($this->scheduled_at)->toIso8601String(),
            'started_at' => optional($this->started_at)->toIso8601String(),
            'finished_at' => optional($this->finished_at)->toIso8601String(),
            'stats' => $this->stats,
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
