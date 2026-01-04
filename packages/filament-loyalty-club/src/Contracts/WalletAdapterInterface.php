<?php

namespace Haida\FilamentLoyaltyClub\Contracts;

use Haida\FilamentLoyaltyClub\Models\LoyaltyCustomer;

interface WalletAdapterInterface
{
    public function credit(LoyaltyCustomer $customer, float $amount, string $idempotencyKey, array $meta = []): float;

    public function debit(LoyaltyCustomer $customer, float $amount, string $idempotencyKey, array $meta = []): float;

    public function getBalance(LoyaltyCustomer $customer): float;
}
