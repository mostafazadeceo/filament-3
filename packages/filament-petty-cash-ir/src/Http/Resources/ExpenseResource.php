<?php

namespace Haida\FilamentPettyCashIr\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'company_id' => $this->company_id,
            'branch_id' => $this->branch_id,
            'fund_id' => $this->fund_id,
            'category_id' => $this->category_id,
            'requested_by' => $this->requested_by,
            'approved_by' => $this->approved_by,
            'paid_by' => $this->paid_by,
            'accounting_party_id' => $this->accounting_party_id,
            'accounting_journal_entry_id' => $this->accounting_journal_entry_id,
            'expense_date' => $this->expense_date,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'status' => $this->status,
            'reference' => $this->reference,
            'payee_name' => $this->payee_name,
            'description' => $this->description,
            'receipt_required' => $this->receipt_required,
            'has_receipt' => $this->has_receipt,
            'approved_at' => $this->approved_at,
            'paid_at' => $this->paid_at,
            'metadata' => $this->metadata,
            'attachments' => ExpenseAttachmentResource::collection($this->whenLoaded('attachments')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
