<?php

namespace Haida\FilamentPayments\DTO;

class PaymentIntentResponse
{
    public function __construct(
        public string $providerReference,
        public ?string $redirectUrl = null,
        public string $status = 'pending',
        public array $meta = []
    ) {}
}
