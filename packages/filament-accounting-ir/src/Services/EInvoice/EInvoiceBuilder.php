<?php

namespace Vendor\FilamentAccountingIr\Services\EInvoice;

use Vendor\FilamentAccountingIr\Models\EInvoice;
use Vendor\FilamentAccountingIr\Models\SalesInvoiceLine;
use Vendor\FilamentAccountingIr\Services\EInvoice\Domain\Invoice;
use Vendor\FilamentAccountingIr\Services\EInvoice\Domain\InvoiceLine;
use Vendor\FilamentAccountingIr\Services\EInvoice\Domain\Party;

class EInvoiceBuilder
{
    public function build(EInvoice $invoice): Invoice
    {
        $invoice->loadMissing(['company', 'salesInvoice.party', 'salesInvoice.lines', 'lines']);

        $company = $invoice->company;
        $seller = new Party(
            name: $company?->name ?? 'Unknown',
            nationalId: $company?->national_id,
            economicCode: $company?->economic_code,
            address: $company?->metadata['address'] ?? null,
            metadata: $company?->metadata ?? [],
        );

        $buyer = null;
        if ($invoice->salesInvoice && $invoice->salesInvoice->party) {
            $party = $invoice->salesInvoice->party;
            $buyer = new Party(
                name: $party->name,
                nationalId: $party->national_id,
                economicCode: $party->economic_code,
                address: $party->metadata['address'] ?? null,
                metadata: $party->metadata ?? [],
            );
        }

        $lines = [];
        if ($invoice->salesInvoice) {
            foreach ($invoice->salesInvoice->lines as $line) {
                $lines[] = $this->mapSalesLine($line);
            }
        } elseif ($invoice->lines->isNotEmpty()) {
            foreach ($invoice->lines as $line) {
                $lines[] = new InvoiceLine(
                    description: $line->description ?? 'Line',
                    quantity: (float) $line->quantity,
                    unitPrice: (float) $line->unit_price,
                    taxAmount: (float) $line->tax_amount,
                    lineTotal: (float) $line->line_total,
                    metadata: $line->metadata ?? [],
                );
            }
        }

        $issuedAt = $invoice->issued_at ?? $invoice->salesInvoice?->invoice_date?->startOfDay() ?? now();
        $total = $invoice->salesInvoice?->total ?? array_sum(array_map(fn (InvoiceLine $line) => $line->lineTotal, $lines));

        return new Invoice(
            number: $invoice->salesInvoice?->invoice_no ?? (string) $invoice->getKey(),
            issuedAt: $issuedAt,
            currency: $invoice->salesInvoice?->currency ?? 'IRR',
            total: (float) $total,
            seller: $seller,
            buyer: $buyer,
            lines: $lines,
            metadata: [
                'invoice_type' => $invoice->invoice_type,
                'status' => $invoice->status,
            ],
        );
    }

    protected function mapSalesLine(SalesInvoiceLine $line): InvoiceLine
    {
        return new InvoiceLine(
            description: $line->description ?? ($line->product?->name ?? 'Line'),
            quantity: (float) $line->quantity,
            unitPrice: (float) $line->unit_price,
            taxAmount: (float) $line->tax_amount,
            lineTotal: (float) $line->line_total,
            metadata: $line->metadata ?? [],
        );
    }
}
