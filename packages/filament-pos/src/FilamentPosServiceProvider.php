<?php

namespace Haida\FilamentPos;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\FilamentPos\Models\PosCashierSession;
use Haida\FilamentPos\Models\PosCashMovement;
use Haida\FilamentPos\Models\PosDevice;
use Haida\FilamentPos\Models\PosRegister;
use Haida\FilamentPos\Models\PosSale;
use Haida\FilamentPos\Models\PosStore;
use Haida\FilamentPos\Policies\PosCashierSessionPolicy;
use Haida\FilamentPos\Policies\PosCashMovementPolicy;
use Haida\FilamentPos\Policies\PosDevicePolicy;
use Haida\FilamentPos\Policies\PosRegisterPolicy;
use Haida\FilamentPos\Policies\PosSalePolicy;
use Haida\FilamentPos\Policies\PosStorePolicy;
use Haida\FilamentPos\Services\PosCashierSessionService;
use Haida\FilamentPos\Services\PosOutboxService;
use Haida\FilamentPos\Services\PosSaleService;
use Haida\FilamentPos\Services\PosSyncService;
use Haida\FilamentPos\Support\PosCapabilities;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentPosServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-pos')
            ->hasConfigFile('filament-pos')
            ->hasRoutes('api')
            ->hasTranslations()
            ->hasMigrations([
                '2026_01_02_000003_create_pos_tables',
            ])
            ->runsMigrations();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(PosCashierSessionService::class);
        $this->app->singleton(PosSaleService::class);
        $this->app->singleton(PosOutboxService::class);
        $this->app->singleton(PosSyncService::class);
    }

    public function packageBooted(): void
    {
        Gate::policy(PosStore::class, PosStorePolicy::class);
        Gate::policy(PosRegister::class, PosRegisterPolicy::class);
        Gate::policy(PosDevice::class, PosDevicePolicy::class);
        Gate::policy(PosCashierSession::class, PosCashierSessionPolicy::class);
        Gate::policy(PosCashMovement::class, PosCashMovementPolicy::class);
        Gate::policy(PosSale::class, PosSalePolicy::class);

        if (class_exists(CapabilityRegistryInterface::class)) {
            $registry = $this->app->make(CapabilityRegistryInterface::class);
            PosCapabilities::register($registry);
        }
    }
}
