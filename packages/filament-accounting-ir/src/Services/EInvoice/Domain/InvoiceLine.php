<?php

namespace Vendor\FilamentAccountingIr\Services\EInvoice\Domain;

class InvoiceLine
{
    /**
     * @param  array<string, mixed>  $metadata
     */
    public function __construct(
        public string $description,
        public float $quantity,
        public float $unitPrice,
        public float $taxAmount,
        public float $lineTotal,
        public array $metadata = [],
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'description' => $this->description,
            'quantity' => $this->quantity,
            'unit_price' => $this->unitPrice,
            'tax_amount' => $this->taxAmount,
            'line_total' => $this->lineTotal,
            'metadata' => $this->metadata,
        ];
    }
}
