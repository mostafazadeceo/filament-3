<?php

declare(strict_types=1);

namespace Haida\ProvidersEsimGoCore;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\ProvidersCore\Services\ProviderRegistry;
use Haida\ProvidersEsimGoCore\Adapters\EsimGoProviderAdapter;
use Haida\ProvidersEsimGoCore\Clients\EsimGoClientFactory;
use Haida\ProvidersEsimGoCore\Models\EsimGoCallback;
use Haida\ProvidersEsimGoCore\Models\EsimGoCatalogueSnapshot;
use Haida\ProvidersEsimGoCore\Models\EsimGoConnection;
use Haida\ProvidersEsimGoCore\Models\EsimGoEsim;
use Haida\ProvidersEsimGoCore\Models\EsimGoInventoryUsage;
use Haida\ProvidersEsimGoCore\Models\EsimGoOrder;
use Haida\ProvidersEsimGoCore\Models\EsimGoProduct;
use Haida\ProvidersEsimGoCore\Policies\EsimGoCallbackPolicy;
use Haida\ProvidersEsimGoCore\Policies\EsimGoCatalogueSnapshotPolicy;
use Haida\ProvidersEsimGoCore\Policies\EsimGoConnectionPolicy;
use Haida\ProvidersEsimGoCore\Policies\EsimGoEsimPolicy;
use Haida\ProvidersEsimGoCore\Policies\EsimGoInventoryUsagePolicy;
use Haida\ProvidersEsimGoCore\Policies\EsimGoOrderPolicy;
use Haida\ProvidersEsimGoCore\Policies\EsimGoProductPolicy;
use Haida\ProvidersEsimGoCore\Services\EsimGoCatalogueService;
use Haida\ProvidersEsimGoCore\Services\EsimGoConnectionService;
use Haida\ProvidersEsimGoCore\Services\EsimGoInventoryService;
use Haida\ProvidersEsimGoCore\Services\EsimGoOrderService;
use Haida\ProvidersEsimGoCore\Services\EsimGoWebhookService;
use Haida\ProvidersEsimGoCore\Support\EsimGoCapabilities;
use Haida\ProvidersEsimGoCore\Support\EsimGoRateLimiter;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ProvidersEsimGoCoreServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('providers-esim-go-core')
            ->hasConfigFile('providers-esim-go-core')
            ->hasTranslations()
            ->hasRoutes('api')
            ->hasMigrations([
                '2025_12_31_000001_create_esim_go_connections_table',
                '2025_12_31_000002_create_esim_go_catalogue_snapshots_table',
                '2025_12_31_000003_create_esim_go_products_table',
                '2025_12_31_000004_create_esim_go_orders_table',
                '2025_12_31_000005_create_esim_go_esims_table',
                '2025_12_31_000006_create_esim_go_callbacks_table',
                '2025_12_31_000007_create_esim_go_inventory_usages_table',
                '2026_01_01_000007_add_countries_meta_to_esim_go_products_table',
            ])
            ->runsMigrations();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(EsimGoRateLimiter::class);
        $this->app->singleton(EsimGoClientFactory::class);
        $this->app->singleton(EsimGoCatalogueService::class);
        $this->app->singleton(EsimGoConnectionService::class);
        $this->app->singleton(EsimGoInventoryService::class);
        $this->app->singleton(EsimGoOrderService::class);
        $this->app->singleton(EsimGoWebhookService::class);

        if (class_exists(ProviderRegistry::class)) {
            $registry = $this->app->make(ProviderRegistry::class);
            $registry->register('esim-go', EsimGoProviderAdapter::class);
        }

        if (class_exists(CapabilityRegistryInterface::class)) {
            $registry = $this->app->make(CapabilityRegistryInterface::class);
            EsimGoCapabilities::register($registry);
        }
    }

    public function packageBooted(): void
    {
        Gate::policy(EsimGoConnection::class, EsimGoConnectionPolicy::class);
        Gate::policy(EsimGoCatalogueSnapshot::class, EsimGoCatalogueSnapshotPolicy::class);
        Gate::policy(EsimGoProduct::class, EsimGoProductPolicy::class);
        Gate::policy(EsimGoOrder::class, EsimGoOrderPolicy::class);
        Gate::policy(EsimGoEsim::class, EsimGoEsimPolicy::class);
        Gate::policy(EsimGoCallback::class, EsimGoCallbackPolicy::class);
        Gate::policy(EsimGoInventoryUsage::class, EsimGoInventoryUsagePolicy::class);
    }
}
