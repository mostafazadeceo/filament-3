<?php

namespace Haida\FilamentLoyaltyClub\Adapters;

use Carbon\CarbonImmutable;
use Haida\FilamentLoyaltyClub\Contracts\PurchaseAdapterInterface;
use Haida\FilamentLoyaltyClub\Support\PurchaseData;
use Vendor\FilamentAccountingIr\Models\SalesInvoice;

class AccountingSalesInvoiceAdapter implements PurchaseAdapterInterface
{
    public function resolve(array $payload): PurchaseData
    {
        $invoiceId = $payload['sales_invoice_id'] ?? $payload['invoice_id'] ?? null;
        if ($invoiceId && class_exists(SalesInvoice::class)) {
            $invoice = SalesInvoice::query()->find($invoiceId);
            if ($invoice) {
                $occurredAt = $invoice->invoice_date ? CarbonImmutable::parse($invoice->invoice_date) : null;

                return new PurchaseData(
                    (float) $invoice->total,
                    (string) ($invoice->currency ?? 'irr'),
                    (string) ($invoice->invoice_no ?? $invoice->getKey()),
                    $occurredAt,
                    [
                        'sales_invoice_id' => $invoice->getKey(),
                        'status' => $invoice->status,
                        'company_id' => $invoice->company_id,
                        'branch_id' => $invoice->branch_id,
                        'party_id' => $invoice->party_id,
                    ]
                );
            }
        }

        $fallback = new FallbackPurchaseAdapter;

        return $fallback->resolve($payload);
    }
}
