<?php

namespace Haida\FilamentLoyaltyClub\Support;

use Carbon\CarbonImmutable;

final class PurchaseData
{
    public function __construct(
        public readonly float $amount,
        public readonly string $currency,
        public readonly ?string $reference,
        public readonly ?CarbonImmutable $occurredAt = null,
        public readonly array $meta = [],
    ) {}
}
