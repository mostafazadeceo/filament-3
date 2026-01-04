<?php

namespace Haida\FilamentStorefrontBuilder;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\FilamentStorefrontBuilder\Models\StoreBlock;
use Haida\FilamentStorefrontBuilder\Models\StoreMenu;
use Haida\FilamentStorefrontBuilder\Models\StorePage;
use Haida\FilamentStorefrontBuilder\Models\StoreRedirect;
use Haida\FilamentStorefrontBuilder\Models\StoreTheme;
use Haida\FilamentStorefrontBuilder\Policies\StoreBlockPolicy;
use Haida\FilamentStorefrontBuilder\Policies\StoreMenuPolicy;
use Haida\FilamentStorefrontBuilder\Policies\StorePagePolicy;
use Haida\FilamentStorefrontBuilder\Policies\StoreRedirectPolicy;
use Haida\FilamentStorefrontBuilder\Policies\StoreThemePolicy;
use Haida\FilamentStorefrontBuilder\Services\StorefrontPublishService;
use Haida\FilamentStorefrontBuilder\Support\StorefrontCapabilities;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentStorefrontBuilderServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-storefront-builder')
            ->hasConfigFile('filament-storefront-builder')
            ->hasRoutes(['api', 'web'])
            ->hasTranslations()
            ->hasMigrations([
                '2026_01_02_000004_create_storefront_builder_tables',
            ])
            ->runsMigrations();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(StorefrontPublishService::class);
    }

    public function packageBooted(): void
    {
        Gate::policy(StorePage::class, StorePagePolicy::class);
        Gate::policy(StoreBlock::class, StoreBlockPolicy::class);
        Gate::policy(StoreMenu::class, StoreMenuPolicy::class);
        Gate::policy(StoreTheme::class, StoreThemePolicy::class);
        Gate::policy(StoreRedirect::class, StoreRedirectPolicy::class);

        if (class_exists(CapabilityRegistryInterface::class)) {
            $registry = $this->app->make(CapabilityRegistryInterface::class);
            StorefrontCapabilities::register($registry);
        }
    }
}
