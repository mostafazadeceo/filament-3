<?php

namespace Haida\CommerceCatalog;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\CommerceCatalog\Models\CatalogCollection;
use Haida\CommerceCatalog\Models\CatalogProduct;
use Haida\CommerceCatalog\Policies\CatalogCollectionPolicy;
use Haida\CommerceCatalog\Policies\CatalogProductPolicy;
use Haida\CommerceCatalog\Services\CatalogPricingService;
use Haida\CommerceCatalog\Support\CatalogCapabilities;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class CommerceCatalogServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('commerce-catalog')
            ->hasConfigFile('commerce-catalog')
            ->hasRoutes('api')
            ->hasMigrations([
                '2025_12_30_000010_create_commerce_catalog_tables',
            ])
            ->runsMigrations();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(CatalogPricingService::class);
    }

    public function packageBooted(): void
    {
        Gate::policy(CatalogProduct::class, CatalogProductPolicy::class);
        Gate::policy(CatalogCollection::class, CatalogCollectionPolicy::class);

        if (interface_exists(CapabilityRegistryInterface::class)) {
            $registry = $this->app->make(CapabilityRegistryInterface::class);
            CatalogCapabilities::register($registry);
        }
    }
}
