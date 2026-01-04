<?php

namespace Haida\FilamentLoyaltyClub\Adapters;

use Filamat\IamSuite\Models\Wallet;
use Filamat\IamSuite\Services\WalletService;
use Haida\FilamentLoyaltyClub\Contracts\WalletAdapterInterface;
use Haida\FilamentLoyaltyClub\Models\LoyaltyCustomer;
use Illuminate\Contracts\Auth\Authenticatable;
use RuntimeException;

class IamWalletAdapter implements WalletAdapterInterface
{
    public function __construct(protected WalletService $walletService) {}

    public function credit(LoyaltyCustomer $customer, float $amount, string $idempotencyKey, array $meta = []): float
    {
        $wallet = $this->resolveWallet($customer);
        $this->walletService->credit($wallet, $amount, $idempotencyKey, $meta);

        return (float) $wallet->refresh()->balance;
    }

    public function debit(LoyaltyCustomer $customer, float $amount, string $idempotencyKey, array $meta = []): float
    {
        $wallet = $this->resolveWallet($customer);
        $this->walletService->debit($wallet, $amount, $idempotencyKey, $meta);

        return (float) $wallet->refresh()->balance;
    }

    public function getBalance(LoyaltyCustomer $customer): float
    {
        $wallet = $this->resolveWallet($customer);

        return (float) $wallet->balance;
    }

    protected function resolveWallet(LoyaltyCustomer $customer): Wallet
    {
        $userId = $customer->user_id;
        if (! $userId) {
            throw new RuntimeException('این مشتری به کاربر سامانه متصل نیست.');
        }

        $userModel = config('auth.providers.users.model');
        /** @var Authenticatable|null $user */
        $user = $userModel::query()->find($userId);
        if (! $user) {
            throw new RuntimeException('کاربر یافت نشد.');
        }

        $tenant = $customer->tenant;
        if (! $tenant) {
            throw new RuntimeException('فضای کاری یافت نشد.');
        }

        $currency = (string) config('filament-loyalty-club.features.cashback.currency', 'irr');

        return $this->walletService->createWallet($user, $tenant, $currency);
    }
}
