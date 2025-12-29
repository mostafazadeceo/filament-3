<?php

namespace Vendor\FilamentAccountingIr\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChequeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'company_id' => $this->company_id,
            'party_id' => $this->party_id,
            'treasury_account_id' => $this->treasury_account_id,
            'direction' => $this->direction,
            'cheque_no' => $this->cheque_no,
            'bank_name' => $this->bank_name,
            'branch_name' => $this->branch_name,
            'due_date' => optional($this->due_date)->toDateString(),
            'amount' => $this->amount,
            'status' => $this->status,
            'metadata' => $this->metadata,
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}
