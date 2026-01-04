<?php

namespace Haida\FilamentLoyaltyClub\Adapters;

use Haida\FilamentLoyaltyClub\Contracts\WalletAdapterInterface;
use Haida\FilamentLoyaltyClub\Models\LoyaltyCustomer;
use Haida\FilamentLoyaltyClub\Models\LoyaltyWalletAccount;

class InternalWalletAdapter implements WalletAdapterInterface
{
    public function credit(LoyaltyCustomer $customer, float $amount, string $idempotencyKey, array $meta = []): float
    {
        $account = $this->ensureAccount($customer);

        return (float) $account->cashback_balance;
    }

    public function debit(LoyaltyCustomer $customer, float $amount, string $idempotencyKey, array $meta = []): float
    {
        $account = $this->ensureAccount($customer);

        return (float) $account->cashback_balance;
    }

    public function getBalance(LoyaltyCustomer $customer): float
    {
        $account = $this->ensureAccount($customer);

        return (float) $account->cashback_balance;
    }

    protected function ensureAccount(LoyaltyCustomer $customer): LoyaltyWalletAccount
    {
        return LoyaltyWalletAccount::query()->firstOrCreate([
            'tenant_id' => $customer->tenant_id,
            'customer_id' => $customer->getKey(),
        ], [
            'points_balance' => 0,
            'points_earned_total' => 0,
            'points_redeemed_total' => 0,
            'cashback_balance' => 0,
            'cashback_earned_total' => 0,
            'cashback_redeemed_total' => 0,
        ]);
    }
}
