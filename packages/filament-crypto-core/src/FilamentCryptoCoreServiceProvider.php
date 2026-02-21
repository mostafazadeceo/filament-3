<?php

namespace Haida\FilamentCryptoCore;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\FilamentCryptoCore\Models\CryptoAccount;
use Haida\FilamentCryptoCore\Models\CryptoAddress;
use Haida\FilamentCryptoCore\Models\CryptoAuditEvent;
use Haida\FilamentCryptoCore\Models\CryptoFeePolicy;
use Haida\FilamentCryptoCore\Models\CryptoLedger;
use Haida\FilamentCryptoCore\Models\CryptoLedgerEntry;
use Haida\FilamentCryptoCore\Models\CryptoNetworkFee;
use Haida\FilamentCryptoCore\Models\CryptoRate;
use Haida\FilamentCryptoCore\Models\CryptoWallet;
use Haida\FilamentCryptoCore\Policies\CryptoAccountPolicy;
use Haida\FilamentCryptoCore\Policies\CryptoAddressPolicy;
use Haida\FilamentCryptoCore\Policies\CryptoAuditLogPolicy;
use Haida\FilamentCryptoCore\Policies\CryptoFeePolicyPolicy;
use Haida\FilamentCryptoCore\Policies\CryptoLedgerEntryPolicy;
use Haida\FilamentCryptoCore\Policies\CryptoLedgerPolicy;
use Haida\FilamentCryptoCore\Policies\CryptoNetworkFeePolicy;
use Haida\FilamentCryptoCore\Policies\CryptoRatePolicy;
use Haida\FilamentCryptoCore\Policies\CryptoWalletPolicy;
use Haida\FilamentCryptoCore\Services\AuditLogService;
use Haida\FilamentCryptoCore\Services\CryptoAuditService;
use Haida\FilamentCryptoCore\Services\CryptoEventBus;
use Haida\FilamentCryptoCore\Services\FeePolicyEngine;
use Haida\FilamentCryptoCore\Services\LedgerService;
use Haida\FilamentCryptoCore\Services\NetworkFeeService;
use Haida\FilamentCryptoCore\Services\RateService;
use Haida\FilamentCryptoCore\Support\CryptoCoreCapabilities;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentCryptoCoreServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-crypto-core')
            ->hasConfigFile('filament-crypto-core')
            ->hasTranslations()
            ->hasMigrations([
                '2026_01_10_000001_create_crypto_core_tables',
            ])
            ->runsMigrations();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(LedgerService::class);
        $this->app->singleton(RateService::class);
        $this->app->singleton(NetworkFeeService::class);
        $this->app->singleton(FeePolicyEngine::class);
        $this->app->singleton(CryptoEventBus::class);
        $this->app->singleton(CryptoAuditService::class);
        $this->app->singleton(AuditLogService::class);
    }

    public function packageBooted(): void
    {
        Gate::policy(CryptoAccount::class, CryptoAccountPolicy::class);
        Gate::policy(CryptoLedger::class, CryptoLedgerPolicy::class);
        Gate::policy(CryptoLedgerEntry::class, CryptoLedgerEntryPolicy::class);
        Gate::policy(CryptoWallet::class, CryptoWalletPolicy::class);
        Gate::policy(CryptoAddress::class, CryptoAddressPolicy::class);
        Gate::policy(CryptoRate::class, CryptoRatePolicy::class);
        Gate::policy(CryptoNetworkFee::class, CryptoNetworkFeePolicy::class);
        Gate::policy(CryptoFeePolicy::class, CryptoFeePolicyPolicy::class);
        Gate::policy(CryptoAuditEvent::class, CryptoAuditLogPolicy::class);

        if (interface_exists(CapabilityRegistryInterface::class)) {
            $registry = $this->app->make(CapabilityRegistryInterface::class);
            CryptoCoreCapabilities::register($registry);
        }
    }
}
