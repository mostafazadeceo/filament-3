<?php

namespace Haida\FilamentPettyCashIr\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FundResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'company_id' => $this->company_id,
            'branch_id' => $this->branch_id,
            'custodian_user_id' => $this->custodian_user_id,
            'accounting_cash_account_id' => $this->accounting_cash_account_id,
            'accounting_source_account_id' => $this->accounting_source_account_id,
            'default_expense_account_id' => $this->default_expense_account_id,
            'accounting_treasury_account_id' => $this->accounting_treasury_account_id,
            'name' => $this->name,
            'code' => $this->code,
            'status' => $this->status,
            'currency' => $this->currency,
            'opening_balance' => $this->opening_balance,
            'current_balance' => $this->current_balance,
            'threshold_balance' => $this->threshold_balance,
            'replenishment_amount' => $this->replenishment_amount,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
