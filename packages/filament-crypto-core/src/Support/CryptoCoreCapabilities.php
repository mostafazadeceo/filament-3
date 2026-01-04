<?php

namespace Haida\FilamentCryptoCore\Support;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\FilamentCryptoCore\Policies\CryptoAccountPolicy;
use Haida\FilamentCryptoCore\Policies\CryptoAddressPolicy;
use Haida\FilamentCryptoCore\Policies\CryptoAuditLogPolicy;
use Haida\FilamentCryptoCore\Policies\CryptoFeePolicyPolicy;
use Haida\FilamentCryptoCore\Policies\CryptoLedgerEntryPolicy;
use Haida\FilamentCryptoCore\Policies\CryptoLedgerPolicy;
use Haida\FilamentCryptoCore\Policies\CryptoNetworkFeePolicy;
use Haida\FilamentCryptoCore\Policies\CryptoRatePolicy;
use Haida\FilamentCryptoCore\Policies\CryptoWalletPolicy;

final class CryptoCoreCapabilities
{
    private static bool $registered = false;

    public static function register(CapabilityRegistryInterface $registry): void
    {
        if (self::$registered) {
            return;
        }

        $registry->register(
            'filament-crypto-core',
            self::permissions(),
            [
                'crypto_core' => true,
                'crypto.providers' => true,
                'crypto.wallets' => true,
                'crypto.ledger' => true,
            ],
            [],
            [
                CryptoAccountPolicy::class,
                CryptoLedgerPolicy::class,
                CryptoLedgerEntryPolicy::class,
                CryptoWalletPolicy::class,
                CryptoAddressPolicy::class,
                CryptoRatePolicy::class,
                CryptoNetworkFeePolicy::class,
                CryptoFeePolicyPolicy::class,
                CryptoAuditLogPolicy::class,
            ],
            [
                'crypto' => 'رمزارز',
                'crypto_core' => 'هسته رمزارز',
                'crypto_accounts' => 'حساب‌های دفترکل',
                'crypto_wallets' => 'کیف‌پول‌ها',
                'crypto_addresses' => 'آدرس‌ها',
                'crypto_ledger' => 'دفترکل',
                'crypto_ledger_entries' => 'اقلام دفترکل',
                'crypto_rates' => 'نرخ‌ها و کارمزد شبکه',
                'crypto_fees' => 'پلن و کارمزد',
                'crypto_audit' => 'ثبت رویدادهای مالی',
            ]
        );

        self::$registered = true;
    }

    /**
     * @return array<int, string>
     */
    public static function permissions(): array
    {
        return [
            'crypto.accounts.view',
            'crypto.accounts.manage',
            'crypto.ledgers.view',
            'crypto.ledgers.manage',
            'crypto.ledger_entries.view',
            'crypto.ledger_entries.manage',
            'crypto.wallets.view',
            'crypto.wallets.manage',
            'crypto.addresses.view',
            'crypto.addresses.manage',
            'crypto.rates.view',
            'crypto.rates.manage',
            'crypto.network_fees.view',
            'crypto.network_fees.manage',
            'crypto.fee_policies.view',
            'crypto.fee_policies.manage',
            'crypto.audit.view',
            'crypto.audit.manage',
        ];
    }
}
