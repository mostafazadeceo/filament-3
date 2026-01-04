<?php

declare(strict_types=1);

namespace Haida\ProvidersCore;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\ProvidersCore\Models\ProviderJobLog;
use Haida\ProvidersCore\Policies\ProviderJobLogPolicy;
use Haida\ProvidersCore\Services\ProviderJobDispatcher;
use Haida\ProvidersCore\Services\ProviderJobReprocessService;
use Haida\ProvidersCore\Services\ProviderRegistry;
use Haida\ProvidersCore\Support\ProvidersCoreCapabilities;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ProvidersCoreServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('providers-core')
            ->hasConfigFile('providers-core')
            ->hasMigrations([
                '2025_12_30_000014_create_providers_core_job_logs_table',
            ])
            ->runsMigrations();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(ProviderRegistry::class);
        $this->app->singleton(ProviderJobDispatcher::class);
        $this->app->singleton(ProviderJobReprocessService::class);
    }

    public function packageBooted(): void
    {
        Gate::policy(ProviderJobLog::class, ProviderJobLogPolicy::class);

        if (class_exists(CapabilityRegistryInterface::class)) {
            $registry = $this->app->make(CapabilityRegistryInterface::class);
            ProvidersCoreCapabilities::register($registry);
        }
    }
}
