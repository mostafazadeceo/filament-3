<?php

namespace Vendor\FilamentAccountingIr\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Vendor\FilamentAccountingIr\Events\PurchaseInvoicePosted;
use Vendor\FilamentAccountingIr\Models\PurchaseInvoice;

class PurchaseInvoiceService
{
    public function receive(PurchaseInvoice $invoice): PurchaseInvoice
    {
        if ($invoice->status === 'received' || $invoice->status === 'paid') {
            return $invoice;
        }

        $invoice->loadMissing('lines');

        if ($invoice->lines->isEmpty()) {
            throw ValidationException::withMessages([
                'lines' => 'حداقل یک ردیف برای فاکتور لازم است.',
            ]);
        }

        return DB::transaction(function () use ($invoice): PurchaseInvoice {
            $totals = $invoice->lines->reduce(function (array $carry, $line) {
                $carry['subtotal'] += (float) $line->line_total;
                $carry['tax_total'] += (float) $line->tax_amount;

                return $carry;
            }, ['subtotal' => 0.0, 'tax_total' => 0.0]);

            $invoice->update([
                'status' => 'received',
                'subtotal' => $totals['subtotal'],
                'tax_total' => $totals['tax_total'],
                'total' => $totals['subtotal'] + $totals['tax_total'] - (float) $invoice->discount_total,
            ]);

            app(PostingService::class)->postPurchaseInvoice($invoice);

            event(new PurchaseInvoicePosted($invoice));

            return $invoice->refresh();
        });
    }
}
