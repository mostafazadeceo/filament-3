<?php

namespace Haida\PlatformCore;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\PlatformCore\Models\PluginRegistry;
use Haida\PlatformCore\Models\TenantPlugin;
use Haida\PlatformCore\Policies\PluginRegistryPolicy;
use Haida\PlatformCore\Policies\TenantPluginPolicy;
use Haida\PlatformCore\Services\PluginLifecycleManager;
use Haida\PlatformCore\Support\PlatformCoreCapabilities;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class PlatformCoreServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('platform-core')
            ->hasConfigFile('platform-core')
            ->hasMigrations([
                '2025_12_30_000001_create_plugin_registry_tables',
                '2025_12_30_000016_add_context_to_plugin_migrations',
            ])
            ->runsMigrations();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(PluginLifecycleManager::class);
    }

    public function packageBooted(): void
    {
        Gate::policy(PluginRegistry::class, PluginRegistryPolicy::class);
        Gate::policy(TenantPlugin::class, TenantPluginPolicy::class);

        if (class_exists(CapabilityRegistryInterface::class)) {
            $registry = $this->app->make(CapabilityRegistryInterface::class);
            PlatformCoreCapabilities::register($registry);
        }
    }
}
