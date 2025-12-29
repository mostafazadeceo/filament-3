<?php

namespace Vendor\FilamentAccountingIr\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Vendor\FilamentAccountingIr\Events\SalesInvoicePosted;
use Vendor\FilamentAccountingIr\Models\EInvoice;
use Vendor\FilamentAccountingIr\Models\SalesInvoice;

class SalesInvoiceService
{
    public function issue(SalesInvoice $invoice): SalesInvoice
    {
        if ($invoice->status === 'issued' || $invoice->status === 'paid') {
            return $invoice;
        }

        $invoice->loadMissing('lines');

        if ($invoice->lines->isEmpty()) {
            throw ValidationException::withMessages([
                'lines' => 'حداقل یک ردیف برای فاکتور لازم است.',
            ]);
        }

        return DB::transaction(function () use ($invoice): SalesInvoice {
            $totals = $invoice->lines->reduce(function (array $carry, $line) {
                $carry['subtotal'] += (float) $line->line_total;
                $carry['tax_total'] += (float) $line->tax_amount;

                return $carry;
            }, ['subtotal' => 0.0, 'tax_total' => 0.0]);

            $invoice->update([
                'status' => 'issued',
                'subtotal' => $totals['subtotal'],
                'tax_total' => $totals['tax_total'],
                'total' => $totals['subtotal'] + $totals['tax_total'] - (float) $invoice->discount_total,
            ]);

            if ($invoice->is_official && (bool) config('filament-accounting-ir.e_invoice.enabled', true)) {
                EInvoice::query()->firstOrCreate([
                    'tenant_id' => $invoice->tenant_id,
                    'company_id' => $invoice->company_id,
                    'sales_invoice_id' => $invoice->getKey(),
                ], [
                    'status' => 'draft',
                    'payload_version' => (string) config('filament-accounting-ir.e_invoice.default_payload_version', 'v1'),
                ]);
            }

            app(PostingService::class)->postSalesInvoice($invoice);

            event(new SalesInvoicePosted($invoice));

            return $invoice->refresh();
        });
    }
}
