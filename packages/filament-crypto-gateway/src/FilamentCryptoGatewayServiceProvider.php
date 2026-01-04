<?php

namespace Haida\FilamentCryptoGateway;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\FilamentCryptoGateway\Contracts\AiInsightProvider;
use Haida\FilamentCryptoGateway\Models\CryptoAiReport;
use Haida\FilamentCryptoGateway\Models\CryptoInvoice;
use Haida\FilamentCryptoGateway\Models\CryptoInvoicePayment;
use Haida\FilamentCryptoGateway\Models\CryptoPayout;
use Haida\FilamentCryptoGateway\Models\CryptoPayoutDestination;
use Haida\FilamentCryptoGateway\Models\CryptoProviderAccount;
use Haida\FilamentCryptoGateway\Models\CryptoReconciliation;
use Haida\FilamentCryptoGateway\Models\CryptoWebhookCall;
use Haida\FilamentCryptoGateway\Policies\CryptoAiReportPolicy;
use Haida\FilamentCryptoGateway\Policies\CryptoInvoicePaymentPolicy;
use Haida\FilamentCryptoGateway\Policies\CryptoInvoicePolicy;
use Haida\FilamentCryptoGateway\Policies\CryptoPayoutDestinationPolicy;
use Haida\FilamentCryptoGateway\Policies\CryptoPayoutPolicy;
use Haida\FilamentCryptoGateway\Policies\CryptoProviderAccountPolicy;
use Haida\FilamentCryptoGateway\Policies\CryptoReconciliationPolicy;
use Haida\FilamentCryptoGateway\Policies\CryptoWebhookCallPolicy;
use Haida\FilamentCryptoGateway\Services\AiReportService;
use Haida\FilamentCryptoGateway\Services\FeePolicyService;
use Haida\FilamentCryptoGateway\Services\HealthService;
use Haida\FilamentCryptoGateway\Services\HttpAiInsightProvider;
use Haida\FilamentCryptoGateway\Services\InvoiceService;
use Haida\FilamentCryptoGateway\Services\PayoutService;
use Haida\FilamentCryptoGateway\Services\PlanService;
use Haida\FilamentCryptoGateway\Services\ProviderRegistry;
use Haida\FilamentCryptoGateway\Services\ReconcileService;
use Haida\FilamentCryptoGateway\Services\WebhookIngestionService;
use Haida\FilamentCryptoGateway\Services\WebhookProcessor;
use Haida\FilamentCryptoGateway\Support\CryptoGatewayCapabilities;
use Haida\FilamentCryptoGateway\Support\CryptoGatewayScheduler;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentCryptoGatewayServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-crypto-gateway')
            ->hasConfigFile('filament-crypto-gateway')
            ->hasTranslations()
            ->hasMigrations([
                '2026_01_10_000002_create_crypto_gateway_tables',
                '2026_01_10_000004_add_crypto_payout_whitelist_and_approvals',
            ])
            ->runsMigrations();
    }

    public function packageRegistered(): void
    {
        $this->loadRoutesFrom($this->package->basePath('/../routes/api.php'));

        $this->app->singleton(ProviderRegistry::class);
        $this->app->singleton(WebhookIngestionService::class);
        $this->app->singleton(WebhookProcessor::class);
        $this->app->singleton(InvoiceService::class);
        $this->app->singleton(PayoutService::class);
        $this->app->singleton(ReconcileService::class);
        $this->app->singleton(AiReportService::class);
        $this->app->singleton(PlanService::class);
        $this->app->singleton(FeePolicyService::class);
        $this->app->singleton(HealthService::class);
        $this->app->singleton(AiInsightProvider::class, HttpAiInsightProvider::class);
    }

    public function packageBooted(): void
    {
        Gate::policy(CryptoProviderAccount::class, CryptoProviderAccountPolicy::class);
        Gate::policy(CryptoInvoice::class, CryptoInvoicePolicy::class);
        Gate::policy(CryptoInvoicePayment::class, CryptoInvoicePaymentPolicy::class);
        Gate::policy(CryptoPayout::class, CryptoPayoutPolicy::class);
        Gate::policy(CryptoPayoutDestination::class, CryptoPayoutDestinationPolicy::class);
        Gate::policy(CryptoWebhookCall::class, CryptoWebhookCallPolicy::class);
        Gate::policy(CryptoReconciliation::class, CryptoReconciliationPolicy::class);
        Gate::policy(CryptoAiReport::class, CryptoAiReportPolicy::class);

        if (class_exists(CapabilityRegistryInterface::class)) {
            $registry = $this->app->make(CapabilityRegistryInterface::class);
            CryptoGatewayCapabilities::register($registry);
        }

        $this->registerProviders();

        $this->app->booted(function () {
            if (! config('filament-crypto-gateway.reconcile.schedule.enabled', true)) {
                return;
            }

            if (! Schema::hasTable('tenants')) {
                return;
            }

            $schedule = app(Schedule::class);
            $interval = max(5, (int) config('filament-crypto-gateway.reconcile.schedule.invoice_interval_minutes', 5));
            $cron = "*/{$interval} * * * *";

            $schedule->call([CryptoGatewayScheduler::class, 'reconcileInvoices'])->cron($cron);

            $dailyAt = (string) config('filament-crypto-gateway.reconcile.schedule.daily_at', '02:00');
            $schedule->call([CryptoGatewayScheduler::class, 'reconcileDaily'])->dailyAt($dailyAt);
        });
    }

    protected function registerProviders(): void
    {
        $registry = $this->app->make(ProviderRegistry::class);
        $providers = config('filament-crypto-gateway.providers', []);

        foreach ($providers as $provider) {
            $class = $provider['class'] ?? null;
            if (! $class || ! class_exists($class)) {
                continue;
            }

            $registry->register($this->app->make($class));
        }
    }
}
