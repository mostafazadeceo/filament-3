<?php

namespace Vendor\FilamentAccountingIr\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Vendor\FilamentAccountingIr\Http\Controllers\Controller;
use Vendor\FilamentAccountingIr\Http\Requests\StorePurchaseInvoiceRequest;
use Vendor\FilamentAccountingIr\Http\Requests\UpdatePurchaseInvoiceRequest;
use Vendor\FilamentAccountingIr\Http\Resources\PurchaseInvoiceResource;
use Vendor\FilamentAccountingIr\Models\PurchaseInvoice;
use Vendor\FilamentAccountingIr\Services\PurchaseInvoiceService;

class PurchaseInvoiceController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $invoices = PurchaseInvoice::query()->with('lines')->latest('invoice_date')->paginate();

        return PurchaseInvoiceResource::collection($invoices);
    }

    public function show(PurchaseInvoice $purchase_invoice): PurchaseInvoiceResource
    {
        $purchase_invoice->load('lines');

        return new PurchaseInvoiceResource($purchase_invoice);
    }

    public function store(StorePurchaseInvoiceRequest $request): PurchaseInvoiceResource
    {
        $data = $request->validated();
        $lines = $data['lines'] ?? [];
        unset($data['lines']);

        $invoice = DB::transaction(function () use ($data, $lines): PurchaseInvoice {
            $invoice = PurchaseInvoice::query()->create($data);
            $this->syncLines($invoice, $lines);

            return $invoice->refresh();
        });

        return new PurchaseInvoiceResource($invoice->load('lines'));
    }

    public function update(UpdatePurchaseInvoiceRequest $request, PurchaseInvoice $purchase_invoice): PurchaseInvoiceResource
    {
        $data = $request->validated();
        $lines = $data['lines'] ?? null;
        unset($data['lines']);

        $invoice = DB::transaction(function () use ($purchase_invoice, $data, $lines): PurchaseInvoice {
            $purchase_invoice->update($data);
            if (is_array($lines)) {
                $this->syncLines($purchase_invoice, $lines);
            }

            return $purchase_invoice->refresh();
        });

        return new PurchaseInvoiceResource($invoice->load('lines'));
    }

    public function destroy(PurchaseInvoice $purchase_invoice): array
    {
        $purchase_invoice->delete();

        return ['status' => 'ok'];
    }

    public function receive(PurchaseInvoice $purchase_invoice): PurchaseInvoiceResource
    {
        $invoice = app(PurchaseInvoiceService::class)->receive($purchase_invoice);

        return new PurchaseInvoiceResource($invoice->load('lines'));
    }

    protected function syncLines(PurchaseInvoice $invoice, array $lines): void
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
