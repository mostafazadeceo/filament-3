<?php

namespace Vendor\FilamentAccountingIr\Services\EInvoice\Contracts;

use Vendor\FilamentAccountingIr\Services\EInvoice\Domain\Invoice;

interface EInvoiceMapper
{
    /**
     * @return array<string, mixed>
     */
    public function map(Invoice $invoice, string $version): array;
}
