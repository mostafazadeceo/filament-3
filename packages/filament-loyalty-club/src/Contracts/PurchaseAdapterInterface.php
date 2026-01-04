<?php

namespace Haida\FilamentLoyaltyClub\Contracts;

use Haida\FilamentLoyaltyClub\Support\PurchaseData;

interface PurchaseAdapterInterface
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function resolve(array $payload): PurchaseData;
}
