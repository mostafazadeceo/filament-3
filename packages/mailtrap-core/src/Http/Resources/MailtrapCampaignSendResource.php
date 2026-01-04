<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MailtrapCampaignSendResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'campaign_id' => $this->campaign_id,
            'audience_contact_id' => $this->audience_contact_id,
            'email' => $this->email,
            'name' => $this->name,
            'status' => $this->status,
            'provider_message_id' => $this->provider_message_id,
            'error_message' => $this->error_message,
            'sent_at' => optional($this->sent_at)->toIso8601String(),
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
