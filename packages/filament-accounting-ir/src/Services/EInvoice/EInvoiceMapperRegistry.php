<?php

namespace Vendor\FilamentAccountingIr\Services\EInvoice;

use Vendor\FilamentAccountingIr\Services\EInvoice\Contracts\EInvoiceMapper;

class EInvoiceMapperRegistry
{
    public function resolve(string $version): EInvoiceMapper
    {
        $mappers = (array) config('filament-accounting-ir.e_invoice.mappers', []);
        $mapperClass = $mappers[$version] ?? null;

        if (! $mapperClass || ! class_exists($mapperClass)) {
            throw new \RuntimeException("EInvoice mapper not found for version [{$version}].");
        }

        return app($mapperClass);
    }
}
