<?php

namespace Haida\FilamentMarketplaceConnectors;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\FilamentMarketplaceConnectors\Models\MarketplaceConnector;
use Haida\FilamentMarketplaceConnectors\Models\MarketplaceSyncJob;
use Haida\FilamentMarketplaceConnectors\Policies\MarketplaceConnectorPolicy;
use Haida\FilamentMarketplaceConnectors\Policies\MarketplaceSyncJobPolicy;
use Haida\FilamentMarketplaceConnectors\Services\MarketplaceConnectorRegistry;
use Haida\FilamentMarketplaceConnectors\Support\MarketplaceCapabilities;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentMarketplaceConnectorsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-marketplace-connectors')
            ->hasConfigFile('filament-marketplace-connectors')
            ->hasRoutes('api')
            ->hasTranslations()
            ->hasMigrations([
                '2026_01_02_000006_create_marketplace_connector_tables',
            ])
            ->runsMigrations();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(MarketplaceConnectorRegistry::class);
    }

    public function packageBooted(): void
    {
        Gate::policy(MarketplaceConnector::class, MarketplaceConnectorPolicy::class);
        Gate::policy(MarketplaceSyncJob::class, MarketplaceSyncJobPolicy::class);

        if (class_exists(CapabilityRegistryInterface::class)) {
            $registry = $this->app->make(CapabilityRegistryInterface::class);
            MarketplaceCapabilities::register($registry);
        }

        $this->registerConnectors();
    }

    protected function registerConnectors(): void
    {
        $registry = $this->app->make(MarketplaceConnectorRegistry::class);
        $providers = config('filament-marketplace-connectors.providers', []);

        foreach ($providers as $provider) {
            $class = $provider['class'] ?? null;
            if (! $class || ! class_exists($class)) {
                continue;
            }

            $registry->register($this->app->make($class));
        }
    }
}
