<?php

namespace Vendor\FilamentAccountingIr\Services\EInvoice\Domain;

class Party
{
    /**
     * @param  array<string, mixed>  $metadata
     */
    public function __construct(
        public string $name,
        public ?string $nationalId = null,
        public ?string $economicCode = null,
        public ?string $address = null,
        public array $metadata = [],
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'national_id' => $this->nationalId,
            'economic_code' => $this->economicCode,
            'address' => $this->address,
            'metadata' => $this->metadata,
        ];
    }
}
