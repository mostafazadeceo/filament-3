<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoNodes;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\FilamentCryptoNodes\Models\CryptoNodeConnector;
use Haida\FilamentCryptoNodes\Policies\CryptoNodeConnectorPolicy;
use Haida\FilamentCryptoNodes\Services\BitcoinCoreRpcService;
use Haida\FilamentCryptoNodes\Services\EvmRpcService;
use Haida\FilamentCryptoNodes\Services\NodeHealthService;
use Haida\FilamentCryptoNodes\Support\CryptoNodesCapabilities;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentCryptoNodesServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-crypto-nodes')
            ->hasConfigFile('filament-crypto-nodes')
            ->hasTranslations()
            ->hasMigrations([
                '2026_01_10_000003_create_crypto_node_connectors_table',
            ])
            ->runsMigrations();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(NodeHealthService::class);
        $this->app->singleton(BitcoinCoreRpcService::class);
        $this->app->singleton(EvmRpcService::class);
    }

    public function packageBooted(): void
    {
        Gate::policy(CryptoNodeConnector::class, CryptoNodeConnectorPolicy::class);

        if (interface_exists(CapabilityRegistryInterface::class)) {
            $registry = $this->app->make(CapabilityRegistryInterface::class);
            CryptoNodesCapabilities::register($registry);
        }
    }
}
