<?php

namespace Vendor\FilamentAccountingIr\Services\EInvoice\Mappers;

use Vendor\FilamentAccountingIr\Services\EInvoice\Contracts\EInvoiceMapper;
use Vendor\FilamentAccountingIr\Services\EInvoice\Domain\Invoice;

class V1Mapper implements EInvoiceMapper
{
    public function map(Invoice $invoice, string $version): array
    {
        return [
            'version' => $version,
            'header' => [
                'invoice_no' => $invoice->number,
                'issued_at' => $invoice->issuedAt->toIso8601String(),
                'currency' => $invoice->currency,
                'total' => $invoice->total,
            ],
            'seller' => $invoice->seller->toArray(),
            'buyer' => $invoice->buyer?->toArray(),
            'lines' => array_map(fn ($line) => $line->toArray(), $invoice->lines),
            'metadata' => $invoice->metadata,
        ];
    }
}
