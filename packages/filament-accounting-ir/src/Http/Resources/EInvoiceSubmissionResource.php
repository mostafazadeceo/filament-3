<?php

namespace Vendor\FilamentAccountingIr\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EInvoiceSubmissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'e_invoice_id' => $this->e_invoice_id,
            'provider_id' => $this->provider_id,
            'status' => $this->status,
            'correlation_id' => $this->correlation_id,
            'request_payload' => $this->request_payload,
            'response_payload' => $this->response_payload,
            'created_at' => optional($this->created_at)->toISOString(),
        ];
    }
}
