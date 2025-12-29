<?php

namespace Vendor\FilamentAccountingIr\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountingCompanySettingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'company_id' => $this->company_id,
            'posting_accounts' => $this->posting_accounts ?? [],
            'posting_requires_approval' => (bool) $this->posting_requires_approval,
            'allow_negative_inventory' => (bool) $this->allow_negative_inventory,
            'metadata' => $this->metadata ?? [],
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}
