<?php

namespace Haida\FilamentPayments\DTO;

class PaymentRefundResult
{
    public function __construct(
        public bool $accepted,
        public string $status,
        public ?string $providerReference = null,
        public array $meta = []
    ) {}
}
