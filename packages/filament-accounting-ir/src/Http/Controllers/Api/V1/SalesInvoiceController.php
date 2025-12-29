<?php

namespace Vendor\FilamentAccountingIr\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Vendor\FilamentAccountingIr\Http\Controllers\Controller;
use Vendor\FilamentAccountingIr\Http\Requests\StoreSalesInvoiceRequest;
use Vendor\FilamentAccountingIr\Http\Requests\UpdateSalesInvoiceRequest;
use Vendor\FilamentAccountingIr\Http\Resources\SalesInvoiceResource;
use Vendor\FilamentAccountingIr\Models\SalesInvoice;
use Vendor\FilamentAccountingIr\Services\SalesInvoiceService;

class SalesInvoiceController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $invoices = SalesInvoice::query()->with('lines')->latest('invoice_date')->paginate();

        return SalesInvoiceResource::collection($invoices);
    }

    public function show(SalesInvoice $sales_invoice): SalesInvoiceResource
    {
        $sales_invoice->load('lines');

        return new SalesInvoiceResource($sales_invoice);
    }

    public function store(StoreSalesInvoiceRequest $request): SalesInvoiceResource
    {
        $data = $request->validated();
        $lines = $data['lines'] ?? [];
        unset($data['lines']);

        $invoice = DB::transaction(function () use ($data, $lines): SalesInvoice {
            $invoice = SalesInvoice::query()->create($data);
            $this->syncLines($invoice, $lines);

            return $invoice->refresh();
        });

        return new SalesInvoiceResource($invoice->load('lines'));
    }

    public function update(UpdateSalesInvoiceRequest $request, SalesInvoice $sales_invoice): SalesInvoiceResource
    {
        $data = $request->validated();
        $lines = $data['lines'] ?? null;
        unset($data['lines']);

        $invoice = DB::transaction(function () use ($sales_invoice, $data, $lines): SalesInvoice {
            $sales_invoice->update($data);
            if (is_array($lines)) {
                $this->syncLines($sales_invoice, $lines);
            }

            return $sales_invoice->refresh();
        });

        return new SalesInvoiceResource($invoice->load('lines'));
    }

    public function destroy(SalesInvoice $sales_invoice): array
    {
        $sales_invoice->delete();

        return ['status' => 'ok'];
    }

    public function issue(SalesInvoice $sales_invoice): SalesInvoiceResource
    {
        $invoice = app(SalesInvoiceService::class)->issue($sales_invoice);

        return new SalesInvoiceResource($invoice->load('lines'));
    }

    protected function syncLines(SalesInvoice $invoice, array $lines): void
    {
        $invoice->lines()->delete();

        $subtotal = 0;
        $discountTotal = 0;
        $taxTotal = 0;

        foreach ($lines as $line) {
            $quantity = (float) ($line['quantity'] ?? 0);
            $unitPrice = (float) ($line['unit_price'] ?? 0);
            $discount = (float) ($line['discount_amount'] ?? 0);
            $taxAmount = (float) ($line['tax_amount'] ?? 0);

            $lineSubtotal = $quantity * $unitPrice;
            $lineTotal = (float) ($line['line_total'] ?? ($lineSubtotal - $discount + $taxAmount));

            $invoice->lines()->create([
                'product_id' => $line['product_id'] ?? null,
                'description' => $line['description'] ?? null,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'discount_amount' => $discount,
                'tax_rate' => $line['tax_rate'] ?? 0,
                'tax_amount' => $taxAmount,
                'line_total' => $lineTotal,
            ]);

            $subtotal += $lineSubtotal;
            $discountTotal += $discount;
            $taxTotal += $taxAmount;
        }

        $invoice->update([
            'subtotal' => $subtotal,
            'discount_total' => $discountTotal,
            'tax_total' => $taxTotal,
            'total' => $subtotal - $discountTotal + $taxTotal,
        ]);
    }
}
