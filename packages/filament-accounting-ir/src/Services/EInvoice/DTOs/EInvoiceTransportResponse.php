<?php

namespace Vendor\FilamentAccountingIr\Services\EInvoice\DTOs;

class EInvoiceTransportResponse
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(
        public string $status,
        public ?string $correlationId = null,
        public ?string $uniqueTaxId = null,
        public ?string $message = null,
        public array $payload = [],
    ) {}
}
