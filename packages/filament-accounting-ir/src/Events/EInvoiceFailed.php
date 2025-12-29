<?php

namespace Vendor\FilamentAccountingIr\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Vendor\FilamentAccountingIr\Contracts\AccountingEvent;
use Vendor\FilamentAccountingIr\Models\EInvoice;

class EInvoiceFailed implements AccountingEvent
{
    use Dispatchable;

    public function __construct(public EInvoice $record) {}

    public function eventName(): string
    {
        return 'e_invoice.failed';
    }

    public function payload(): array
    {
        return [
            'event' => 'e_invoice.failed',
            'id' => $this->record->getKey(),
            'tenant_id' => $this->record->tenant_id ?? null,
            'company_id' => $this->record->company_id ?? null,
            'status' => $this->record->status ?? null,
            'created_at' => $this->record->created_at?->toIso8601String(),
        ];
    }

    public function tenantId(): ?int
    {
        return $this->record->tenant_id ?? null;
    }
}
