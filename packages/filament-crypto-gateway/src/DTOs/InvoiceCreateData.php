<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\DTOs;

final class InvoiceCreateData
{
    public function __construct(
        public readonly int $tenantId,
        public readonly string $provider,
        public readonly string $orderId,
        public readonly string $amount,
        public readonly string $currency,
        public readonly ?string $toCurrency = null,
        public readonly ?string $network = null,
        public readonly ?int $lifetime = null,
        public readonly bool $isPaymentMultiple = false,
        public readonly ?string $callbackUrl = null,
        public readonly ?float $tolerancePercent = null,
        public readonly ?float $subtractPercent = null,
        public readonly array $meta = [],
    ) {}
}
