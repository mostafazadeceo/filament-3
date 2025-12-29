<?php

namespace Vendor\FilamentAccountingIr\Services\EInvoice\Domain;

use Carbon\CarbonInterface;

class Invoice
{
    /**
     * @param  array<int, InvoiceLine>  $lines
     * @param  array<string, mixed>  $metadata
     */
    public function __construct(
        public string $number,
        public CarbonInterface $issuedAt,
        public string $currency,
        public float $total,
        public Party $seller,
        public ?Party $buyer,
        public array $lines,
        public array $metadata = [],
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'number' => $this->number,
            'issued_at' => $this->issuedAt->toIso8601String(),
            'currency' => $this->currency,
            'total' => $this->total,
            'seller' => $this->seller->toArray(),
            'buyer' => $this->buyer?->toArray(),
            'lines' => array_map(fn (InvoiceLine $line) => $line->toArray(), $this->lines),
            'metadata' => $this->metadata,
        ];
    }
}
