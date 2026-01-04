<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\DTOs;

use Haida\FilamentCryptoGateway\Enums\CryptoPayoutStatus;

final class ProviderPayoutData
{
    public function __construct(
        public readonly string $provider,
        public readonly string $externalId,
        public readonly string $orderId,
        public readonly string $amount,
        public readonly string $currency,
        public readonly ?string $network,
        public readonly ?string $toAddress,
        public readonly CryptoPayoutStatus $status,
        public readonly bool $isFinal,
        public readonly ?string $txid,
        public readonly ?string $failReason,
        public readonly array $raw = [],
    ) {}
}
