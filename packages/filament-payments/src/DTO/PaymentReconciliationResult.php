<?php

namespace Haida\FilamentPayments\DTO;

class PaymentReconciliationResult
{
    public function __construct(
        public bool $accepted,
        public string $status,
        public array $summary = [],
        public array $meta = []
    ) {}
}
