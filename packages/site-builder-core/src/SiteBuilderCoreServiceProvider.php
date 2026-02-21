<?php

namespace Haida\SiteBuilderCore;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\SiteBuilderCore\Models\Site;
use Haida\SiteBuilderCore\Policies\SitePolicy;
use Haida\SiteBuilderCore\Support\SiteBuilderCapabilities;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SiteBuilderCoreServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('site-builder-core')
            ->hasConfigFile('site-builder-core')
            ->hasMigrations([
                '2025_12_30_000006_create_site_builder_core_tables',
            ])
            ->runsMigrations();
    }

    public function packageBooted(): void
    {
        Gate::policy(Site::class, SitePolicy::class);

        if (interface_exists(CapabilityRegistryInterface::class)) {
            $registry = $this->app->make(CapabilityRegistryInterface::class);
            SiteBuilderCapabilities::register($registry);
        }
    }
}
