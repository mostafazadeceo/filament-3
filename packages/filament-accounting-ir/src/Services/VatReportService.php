<?php

namespace Vendor\FilamentAccountingIr\Services;

use Illuminate\Support\Facades\DB;
use Vendor\FilamentAccountingIr\Events\VatReportSubmitted;
use Vendor\FilamentAccountingIr\Models\PurchaseInvoice;
use Vendor\FilamentAccountingIr\Models\SalesInvoice;
use Vendor\FilamentAccountingIr\Models\VatPeriod;
use Vendor\FilamentAccountingIr\Models\VatReport;
use Vendor\FilamentAccountingIr\Models\VatReportLine;

class VatReportService
{
    public function generate(VatPeriod $period): VatReport
    {
        return DB::transaction(function () use ($period): VatReport {
            $period->loadMissing('reports');

            $report = $period->reports()->latest()->first();

            if (! $report) {
                $report = VatReport::query()->create([
                    'vat_period_id' => $period->getKey(),
                    'status' => 'draft',
                ]);
            }

            $report->lines()->delete();

            $salesInvoices = SalesInvoice::query()
                ->where('company_id', $period->company_id)
                ->whereBetween('invoice_date', [$period->period_start, $period->period_end])
                ->whereIn('status', ['issued', 'paid'])
                ->get();

            $purchaseInvoices = PurchaseInvoice::query()
                ->where('company_id', $period->company_id)
                ->whereBetween('invoice_date', [$period->period_start, $period->period_end])
                ->whereIn('status', ['received', 'paid'])
                ->get();

            $salesBase = 0;
            $salesTax = 0;
            $purchaseBase = 0;
            $purchaseTax = 0;

            foreach ($salesInvoices as $invoice) {
                VatReportLine::query()->create([
                    'vat_report_id' => $report->getKey(),
                    'source_type' => 'sales_invoice',
                    'source_id' => $invoice->getKey(),
                    'base_amount' => $invoice->subtotal,
                    'tax_amount' => $invoice->tax_total,
                ]);

                $salesBase += (float) $invoice->subtotal;
                $salesTax += (float) $invoice->tax_total;
            }

            foreach ($purchaseInvoices as $invoice) {
                VatReportLine::query()->create([
                    'vat_report_id' => $report->getKey(),
                    'source_type' => 'purchase_invoice',
                    'source_id' => $invoice->getKey(),
                    'base_amount' => $invoice->subtotal,
                    'tax_amount' => $invoice->tax_total,
                ]);

                $purchaseBase += (float) $invoice->subtotal;
                $purchaseTax += (float) $invoice->tax_total;
            }

            $report->update([
                'sales_base' => $salesBase,
                'sales_tax' => $salesTax,
                'purchase_base' => $purchaseBase,
                'purchase_tax' => $purchaseTax,
            ]);

            return $report->refresh();
        });
    }

    public function submit(VatReport $report): VatReport
    {
        $report->update([
            'status' => 'submitted',
        ]);

        event(new VatReportSubmitted($report));

        return $report->refresh();
    }
}
