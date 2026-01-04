<?php

namespace Haida\FilamentPayments\DTO;

class PaymentWebhookResult
{
    public function __construct(
        public bool $processed,
        public string $status,
        public ?string $providerReference = null,
        public ?string $eventType = null,
        public array $meta = []
    ) {}
}
