<?php

namespace Haida\FilamentPettyCashIr\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SettlementResource extends JsonResource
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
            'posted_by' => $this->posted_by,
            'accounting_journal_entry_id' => $this->accounting_journal_entry_id,
            'period_start' => $this->period_start,
            'period_end' => $this->period_end,
            'status' => $this->status,
            'total_expenses' => $this->total_expenses,
            'total_replenished' => $this->total_replenished,
            'approved_at' => $this->approved_at,
            'posted_at' => $this->posted_at,
            'reversed_at' => $this->reversed_at,
            'reversed_by' => $this->reversed_by,
            'reversal_reason' => $this->reversal_reason,
            'notes' => $this->notes,
            'metadata' => $this->metadata,
            'expense_ids' => $this->whenLoaded('items', fn () => $this->items->pluck('expense_id')->values()),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
