<?php

namespace Vendor\FilamentAccountingIr\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EInvoiceStatusLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'e_invoice_id' => $this->e_invoice_id,
            'status' => $this->status,
            'message' => $this->message,
            'metadata' => $this->metadata,
            'created_at' => optional($this->created_at)->toISOString(),
        ];
    }
}
