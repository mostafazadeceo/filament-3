<?php

namespace Vendor\FilamentAccountingIr\Services\EInvoice;

use Vendor\FilamentAccountingIr\Services\EInvoice\Contracts\EInvoiceTransport;

class EInvoiceTransportRegistry
{
    public function resolve(string $driver): EInvoiceTransport
    {
        $providers = (array) config('filament-accounting-ir.e_invoice.providers', []);
        $transportClass = $providers[$driver] ?? null;

        if (! $transportClass || ! class_exists($transportClass)) {
            throw new \RuntimeException("EInvoice transport not found for driver [{$driver}].");
        }

        return app($transportClass);
    }
}
