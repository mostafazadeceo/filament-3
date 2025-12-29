<?php

namespace Vendor\FilamentAccountingIr\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Vendor\FilamentAccountingIr\Models\EInvoice;
use Vendor\FilamentAccountingIr\Models\EInvoiceStatusLog;
use Vendor\FilamentAccountingIr\Services\EInvoice\EInvoiceEngine;

class SendEInvoiceJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public int $invoiceId) {}

    public function handle(EInvoiceEngine $engine): void
    {
        $invoice = EInvoice::query()->find($this->invoiceId);
        if (! $invoice) {
            return;
        }

        try {
            $engine->send($invoice);
        } catch (\Throwable $exception) {
            $invoice->update(['status' => 'failed']);

            EInvoiceStatusLog::query()->create([
                'e_invoice_id' => $invoice->getKey(),
                'status' => 'failed',
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }
}
