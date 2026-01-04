<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\DTOs;

final class PayoutCreateData
{
    public function __construct(
        public readonly int $tenantId,
        public readonly string $provider,
        public readonly string $orderId,
        public readonly string $toAddress,
        public readonly string $amount,
        public readonly string $currency,
        public readonly ?string $network = null,
        public readonly array $meta = [],
    ) {}
}
