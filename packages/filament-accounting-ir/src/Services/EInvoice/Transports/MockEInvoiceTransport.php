<?php

namespace Vendor\FilamentAccountingIr\Services\EInvoice\Transports;

use Illuminate\Support\Str;
use Vendor\FilamentAccountingIr\Models\EInvoiceProvider;
use Vendor\FilamentAccountingIr\Models\KeyMaterial;
use Vendor\FilamentAccountingIr\Services\EInvoice\Contracts\EInvoiceTransport;
use Vendor\FilamentAccountingIr\Services\EInvoice\DTOs\EInvoiceTransportResponse;

class MockEInvoiceTransport implements EInvoiceTransport
{
    public function send(EInvoiceProvider $provider, array $payload, ?KeyMaterial $keyMaterial = null): EInvoiceTransportResponse
    {
        $correlationId = (string) Str::uuid();
        $uniqueTaxId = 'MOCK-'.Str::upper(Str::random(12));

        return new EInvoiceTransportResponse(
            status: 'sent',
            correlationId: $correlationId,
            uniqueTaxId: $uniqueTaxId,
            message: 'mock provider',
            payload: [
                'provider' => $provider->name,
                'received_at' => now()->toIso8601String(),
            ],
        );
    }
}
