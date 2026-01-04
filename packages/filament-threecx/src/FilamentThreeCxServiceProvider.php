<?php

namespace Haida\FilamentThreeCx;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\FilamentThreeCx\Console\Commands\ThreeCxHealthCommand;
use Haida\FilamentThreeCx\Console\Commands\ThreeCxOpenApiCacheCommand;
use Haida\FilamentThreeCx\Console\Commands\ThreeCxPurgeCommand;
use Haida\FilamentThreeCx\Console\Commands\ThreeCxSyncCommand;
use Haida\FilamentThreeCx\Contracts\ContactDirectoryInterface;
use Haida\FilamentThreeCx\Contracts\ThreeCxCapabilityDetectorInterface;
use Haida\FilamentThreeCx\Contracts\ThreeCxTokenProviderInterface;
use Haida\FilamentThreeCx\Models\ThreeCxApiAuditLog;
use Haida\FilamentThreeCx\Models\ThreeCxCallLog;
use Haida\FilamentThreeCx\Models\ThreeCxContact;
use Haida\FilamentThreeCx\Models\ThreeCxInstance;
use Haida\FilamentThreeCx\Policies\ThreeCxApiAuditLogPolicy;
use Haida\FilamentThreeCx\Policies\ThreeCxCallLogPolicy;
use Haida\FilamentThreeCx\Policies\ThreeCxContactPolicy;
use Haida\FilamentThreeCx\Policies\ThreeCxInstancePolicy;
use Haida\FilamentThreeCx\Services\ThreeCxCapabilityDetector;
use Haida\FilamentThreeCx\Services\ThreeCxContactDirectory;
use Haida\FilamentThreeCx\Services\ThreeCxTokenProvider;
use Haida\FilamentThreeCx\Support\ThreeCxCapabilities;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentThreeCxServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-threecx')
            ->hasConfigFile('filament-threecx')
            ->hasTranslations()
            ->hasViews()
            ->hasRoutes('api')
            ->hasCommands([
                ThreeCxHealthCommand::class,
                ThreeCxSyncCommand::class,
                ThreeCxOpenApiCacheCommand::class,
                ThreeCxPurgeCommand::class,
            ])
            ->hasMigrations([
                '2025_12_30_000020_create_threecx_tables',
            ])
            ->runsMigrations();
    }

    public function packageRegistered(): void
    {
        $this->app->bind(ThreeCxTokenProviderInterface::class, ThreeCxTokenProvider::class);
        $this->app->bind(ThreeCxCapabilityDetectorInterface::class, ThreeCxCapabilityDetector::class);
        $this->app->bind(ContactDirectoryInterface::class, ThreeCxContactDirectory::class);
    }

    public function packageBooted(): void
    {
        Gate::policy(ThreeCxInstance::class, ThreeCxInstancePolicy::class);
        Gate::policy(ThreeCxCallLog::class, ThreeCxCallLogPolicy::class);
        Gate::policy(ThreeCxContact::class, ThreeCxContactPolicy::class);
        Gate::policy(ThreeCxApiAuditLog::class, ThreeCxApiAuditLogPolicy::class);

        if (class_exists(CapabilityRegistryInterface::class)) {
            $registry = $this->app->make(CapabilityRegistryInterface::class);
            ThreeCxCapabilities::register($registry);
        }
    }
}
