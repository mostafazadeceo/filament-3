<?php

namespace Vendor\FilamentAccountingIr\Services\EInvoice\Contracts;

use Vendor\FilamentAccountingIr\Models\EInvoiceProvider;
use Vendor\FilamentAccountingIr\Models\KeyMaterial;
use Vendor\FilamentAccountingIr\Services\EInvoice\DTOs\EInvoiceTransportResponse;

interface EInvoiceTransport
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function send(EInvoiceProvider $provider, array $payload, ?KeyMaterial $keyMaterial = null): EInvoiceTransportResponse;
}
