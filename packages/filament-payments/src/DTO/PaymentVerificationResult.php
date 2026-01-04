<?php

namespace Haida\FilamentPayments\DTO;

class PaymentVerificationResult
{
    public function __construct(
        public bool $verified,
        public string $status,
        public ?string $providerReference = null,
        public array $meta = []
    ) {}
}
