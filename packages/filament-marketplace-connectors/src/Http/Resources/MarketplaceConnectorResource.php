<?php

namespace Haida\FilamentMarketplaceConnectors\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketplaceConnectorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'provider_key' => $this->provider_key,
            'name' => $this->name,
            'status' => $this->status,
            'config' => $this->config,
            'metadata' => $this->metadata,
        ];
    }
}
