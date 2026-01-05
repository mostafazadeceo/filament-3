<?php

namespace Haida\FilamentRelograde;

use Haida\FilamentRelograde\Adapters\RelogradeProviderAdapter;
use Haida\FilamentRelograde\Clients\RelogradeClientFactory;
use Haida\FilamentRelograde\Commands\RelogradeInstallCommand;
use Haida\FilamentRelograde\Services\RelogradeAlertService;
use Haida\FilamentRelograde\Services\RelogradeApiLogger;
use Haida\FilamentRelograde\Services\RelogradeAuditLogger;
use Haida\FilamentRelograde\Support\RelogradeRateLimiter;
use Haida\ProvidersCore\Services\ProviderRegistry;
use Illuminate\Console\Scheduling\Schedule;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class RelogradeServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-relograde')
            ->hasConfigFile('relograde')
            ->hasViews()
            ->hasRoutes('web')
            ->hasMigrations([
                '2025_01_01_000001_create_relograde_connections_table',
                '2025_01_01_000002_create_relograde_brands_table',
                '2025_01_01_000003_create_relograde_brand_options_table',
                '2025_01_01_000004_create_relograde_products_table',
                '2025_01_01_000005_create_relograde_accounts_table',
                '2025_01_01_000006_create_relograde_orders_table',
                '2025_01_01_000007_create_relograde_order_items_table',
                '2025_01_01_000008_create_relograde_order_lines_table',
                '2025_01_01_000009_create_relograde_webhook_events_table',
                '2025_01_01_000010_create_relograde_api_logs_table',
                '2025_01_01_000011_create_relograde_audit_logs_table',
                '2025_01_01_000012_create_relograde_alerts_table',
                '2025_01_01_000013_alter_redeem_value_columns_to_string',
            ])
            ->runsMigrations()
            ->hasCommands([
                RelogradeInstallCommand::class,
            ]);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(RelogradeRateLimiter::class);
        $this->app->singleton(RelogradeApiLogger::class);
        $this->app->singleton(RelogradeAuditLogger::class);
        $this->app->singleton(RelogradeAlertService::class);
        $this->app->singleton(RelogradeClientFactory::class);

        if (class_exists(ProviderRegistry::class)) {
            $registry = $this->app->make(ProviderRegistry::class);
            $registry->register('relograde', RelogradeProviderAdapter::class);
        }
    }

    public function packageBooted(): void
    {
        if (! config('relograde.schedule.enabled', true)) {
            return;
        }

        $this->app->booted(function () {
            $schedule = app(Schedule::class);
            $schedule->call([RelogradeScheduler::class, 'syncAccounts'])
                ->{$this->scheduleMethod('sync_accounts')}();
            $schedule->call([RelogradeScheduler::class, 'syncCatalog'])
                ->{$this->scheduleMethod('sync_catalog')}();
            $schedule->call([RelogradeScheduler::class, 'pollPendingOrders'])
                ->{$this->scheduleMethod('poll_pending_orders')}();
            $schedule->call([RelogradeScheduler::class, 'checkLowBalanceAlerts'])
                ->{$this->scheduleMethod('check_low_balance_alerts')}();
        });
    }

    protected function scheduleMethod(string $key): string
    {
        return (string) config("relograde.schedule.{$key}", 'daily');
    }
}
