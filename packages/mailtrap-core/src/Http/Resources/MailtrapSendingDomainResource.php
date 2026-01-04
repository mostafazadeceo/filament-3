<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MailtrapSendingDomainResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'connection_id' => $this->connection_id,
            'domain_id' => $this->domain_id,
            'domain_name' => $this->domain_name,
            'dns_verified' => $this->dns_verified,
            'dns_verified_at' => optional($this->dns_verified_at)->toIso8601String(),
            'compliance_status' => $this->compliance_status,
            'demo' => $this->demo,
            'dns_records' => $this->dns_records,
            'settings' => $this->settings,
            'metadata' => $this->metadata,
            'synced_at' => optional($this->synced_at)->toIso8601String(),
        ];
    }
}
