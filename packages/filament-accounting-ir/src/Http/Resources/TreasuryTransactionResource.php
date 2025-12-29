<?php

namespace Vendor\FilamentAccountingIr\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TreasuryTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'company_id' => $this->company_id,
            'treasury_account_id' => $this->treasury_account_id,
            'transaction_type' => $this->transaction_type,
            'transaction_date' => optional($this->transaction_date)->toDateString(),
            'amount' => $this->amount,
            'currency' => $this->currency,
            'reference' => $this->reference,
            'description' => $this->description,
            'metadata' => $this->metadata,
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}
