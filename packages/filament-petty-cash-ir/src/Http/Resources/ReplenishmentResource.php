<?php

namespace Haida\FilamentPettyCashIr\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReplenishmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'company_id' => $this->company_id,
            'branch_id' => $this->branch_id,
            'fund_id' => $this->fund_id,
            'requested_by' => $this->requested_by,
            'approved_by' => $this->approved_by,
            'paid_by' => $this->paid_by,
            'source_treasury_account_id' => $this->source_treasury_account_id,
            'accounting_journal_entry_id' => $this->accounting_journal_entry_id,
            'accounting_treasury_transaction_id' => $this->accounting_treasury_transaction_id,
            'request_date' => $this->request_date,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'status' => $this->status,
            'approved_at' => $this->approved_at,
            'paid_at' => $this->paid_at,
            'description' => $this->description,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
