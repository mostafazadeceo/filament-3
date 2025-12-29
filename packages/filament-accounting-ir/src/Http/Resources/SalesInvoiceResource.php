<?php

namespace Vendor\FilamentAccountingIr\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SalesInvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'company_id' => $this->company_id,
            'branch_id' => $this->branch_id,
            'party_id' => $this->party_id,
            'fiscal_year_id' => $this->fiscal_year_id,
            'invoice_no' => $this->invoice_no,
            'invoice_date' => optional($this->invoice_date)->toDateString(),
            'due_date' => optional($this->due_date)->toDateString(),
            'status' => $this->status,
            'currency' => $this->currency,
            'exchange_rate' => $this->exchange_rate !== null ? (float) $this->exchange_rate : null,
            'subtotal' => (float) $this->subtotal,
            'discount_total' => (float) $this->discount_total,
            'tax_total' => (float) $this->tax_total,
            'total' => (float) $this->total,
            'is_official' => (bool) $this->is_official,
            'lines' => SalesInvoiceLineResource::collection($this->whenLoaded('lines')),
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}
