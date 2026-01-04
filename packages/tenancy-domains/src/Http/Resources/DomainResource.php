<?php

namespace Haida\TenancyDomains\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DomainResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'site_id' => $this->site_id,
            'host' => $this->host,
            'type' => $this->type,
            'status' => $this->status,
            'verification_method' => $this->verification_method,
            'dns_token' => $this->dns_token,
            'verified_at' => $this->verified_at?->toISOString(),
            'is_primary' => $this->is_primary,
            'last_checked_at' => $this->last_checked_at?->toISOString(),
            'tls' => [
                'status' => $this->tls_status,
                'provider' => $this->tls_provider,
                'mode' => $this->tls_mode,
                'requested_at' => $this->tls_requested_at?->toISOString(),
                'issued_at' => $this->tls_issued_at?->toISOString(),
                'expires_at' => $this->tls_expires_at?->toISOString(),
                'error' => $this->tls_error,
            ],
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
