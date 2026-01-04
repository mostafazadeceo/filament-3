<?php

namespace Haida\FilamentLoyaltyClub\Adapters;

use Carbon\CarbonImmutable;
use Haida\FilamentLoyaltyClub\Contracts\PurchaseAdapterInterface;
use Haida\FilamentLoyaltyClub\Support\PurchaseData;

class FallbackPurchaseAdapter implements PurchaseAdapterInterface
{
    public function resolve(array $payload): PurchaseData
    {
        $amount = (float) ($payload['amount'] ?? $payload['total'] ?? 0);
        $currency = (string) ($payload['currency'] ?? 'irr');
        $reference = isset($payload['reference']) ? (string) $payload['reference'] : null;
        $occurredAt = isset($payload['occurred_at']) ? CarbonImmutable::parse($payload['occurred_at']) : null;

        return new PurchaseData($amount, $currency, $reference, $occurredAt, $payload);
    }
}
