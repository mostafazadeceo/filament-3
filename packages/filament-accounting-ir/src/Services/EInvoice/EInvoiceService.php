<?php

namespace Vendor\FilamentAccountingIr\Services\EInvoice;

use Vendor\FilamentAccountingIr\Jobs\SendEInvoiceJob;
use Vendor\FilamentAccountingIr\Models\EInvoice;

class EInvoiceService
{
    public function queue(EInvoice $invoice): void
    {
        if ($invoice->status === 'sent') {
            return;
        }

        $invoice->update([
            'status' => 'queued',
        ]);

        SendEInvoiceJob::dispatch($invoice->getKey());
    }
}
