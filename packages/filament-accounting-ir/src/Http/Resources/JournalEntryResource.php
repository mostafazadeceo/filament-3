<?php

namespace Vendor\FilamentAccountingIr\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JournalEntryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'company_id' => $this->company_id,
            'branch_id' => $this->branch_id,
            'fiscal_year_id' => $this->fiscal_year_id,
            'fiscal_period_id' => $this->fiscal_period_id,
            'entry_no' => $this->entry_no,
            'entry_date' => optional($this->entry_date)->toDateString(),
            'status' => $this->status,
            'description' => $this->description,
            'total_debit' => (float) $this->total_debit,
            'total_credit' => (float) $this->total_credit,
            'submitted_at' => optional($this->submitted_at)->toISOString(),
            'approved_at' => optional($this->approved_at)->toISOString(),
            'posted_at' => optional($this->posted_at)->toISOString(),
            'lines' => JournalLineResource::collection($this->whenLoaded('lines')),
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}
