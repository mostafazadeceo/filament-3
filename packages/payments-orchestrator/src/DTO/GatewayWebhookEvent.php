<?php

namespace Haida\PaymentsOrchestrator\DTO;

class GatewayWebhookEvent
{
    public function __construct(
        public string $eventId,
        public string $status,
        public ?int $intentId = null,
        public ?int $orderId = null,
        public ?float $amount = null,
        public ?string $currency = null,
        public ?string $reference = null,
        public array $meta = []
    ) {}
}
