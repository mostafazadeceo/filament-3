<?php

namespace Vendor\FilamentAccountingIr\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EInvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'company_id' => $this->company_id,
            'sales_invoice_id' => $this->sales_invoice_id,
            'provider_id' => $this->provider_id,
            'invoice_type' => $this->invoice_type,
            'status' => $this->status,
            'unique_tax_id' => $this->unique_tax_id,
            'payload_version' => $this->payload_version,
            'issued_at' => optional($this->issued_at)->toISOString(),
            'payload' => $this->payload,
            'metadata' => $this->metadata,
            'lines' => EInvoiceLineResource::collection($this->whenLoaded('lines')),
            'submissions' => EInvoiceSubmissionResource::collection($this->whenLoaded('submissions')),
            'status_logs' => EInvoiceStatusLogResource::collection($this->whenLoaded('statusLogs')),
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}
