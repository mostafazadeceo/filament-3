<?php

namespace Haida\PaymentsOrchestrator\DTO;

class GatewayIntentResponse
{
    public function __construct(
        public string $providerReference,
        public ?string $redirectUrl = null,
        public string $status = 'pending',
        public array $meta = []
    ) {}
}
