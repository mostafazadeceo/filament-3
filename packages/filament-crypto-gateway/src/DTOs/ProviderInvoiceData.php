<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\DTOs;

use Haida\FilamentCryptoGateway\Enums\CryptoInvoiceStatus;

final class ProviderInvoiceData
{
    public function __construct(
        public readonly string $provider,
        public readonly string $externalId,
        public readonly string $orderId,
        public readonly string $amount,
        public readonly string $currency,
        public readonly ?string $toCurrency,
        public readonly ?string $network,
        public readonly ?string $address,
        public readonly CryptoInvoiceStatus $status,
        public readonly bool $isFinal,
        public readonly ?string $expiresAt,
        public readonly array $raw = [],
    ) {}
}
