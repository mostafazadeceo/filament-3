<?php

declare(strict_types=1);

namespace Haida\FilamentAppApi;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentAppApi\Console\Commands\AppApiOpenApiCommand;
use Haida\FilamentAppApi\Support\AppApiCapabilities;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentAppApiServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-app-api')
            ->hasConfigFile('filament-app-api')
            ->hasTranslations()
            ->hasRoutes('api')
            ->hasCommands([
                AppApiOpenApiCommand::class,
            ])
            ->hasMigrations([
                '2026_02_01_000001_create_app_devices_table',
                '2026_02_01_000002_create_app_device_tokens_table',
                '2026_02_01_000003_create_app_support_tables',
                '2026_02_01_000004_create_app_sync_tables',
                '2026_02_01_000005_create_app_refresh_tokens_table',
                '2026_02_01_000006_create_app_tasks_table',
                '2026_02_01_000007_create_app_attendance_records_table',
                '2026_02_01_000008_create_app_signaling_messages_table',
            ])
            ->runsMigrations();
    }

    public function packageBooted(): void
    {
        Gate::define('app.view', fn () => IamAuthorization::allows('app.view'));

        if (class_exists(CapabilityRegistryInterface::class)) {
            $registry = $this->app->make(CapabilityRegistryInterface::class);
            AppApiCapabilities::register($registry);
        }
    }
}
