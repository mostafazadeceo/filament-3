<?php

namespace Haida\FilamentPayments;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Filamat\IamSuite\Observers\AuditableObserver;
use Haida\FilamentPayments\Models\PaymentIntent;
use Haida\FilamentPayments\Models\PaymentProviderConnection;
use Haida\FilamentPayments\Models\PaymentReconciliation;
use Haida\FilamentPayments\Models\PaymentRefund;
use Haida\FilamentPayments\Models\PaymentWebhookEvent;
use Haida\FilamentPayments\Policies\PaymentIntentPolicy;
use Haida\FilamentPayments\Policies\PaymentProviderConnectionPolicy;
use Haida\FilamentPayments\Policies\PaymentReconciliationPolicy;
use Haida\FilamentPayments\Policies\PaymentRefundPolicy;
use Haida\FilamentPayments\Policies\PaymentWebhookEventPolicy;
use Haida\FilamentPayments\Services\PaymentIntentService;
use Haida\FilamentPayments\Services\PaymentProviderRegistry;
use Haida\FilamentPayments\Services\WebhookHandler;
use Haida\FilamentPayments\Support\PaymentCapabilities;
use Haida\FilamentPayments\Support\WebhookSignature;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentPaymentsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-payments')
            ->hasConfigFile('filament-payments')
            ->hasRoutes('api')
            ->hasTranslations()
            ->hasMigrations([
                '2026_01_02_000002_create_payments_tables',
                '2026_01_03_000003_update_payment_webhook_unique_index',
            ])
            ->runsMigrations();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(PaymentProviderRegistry::class);
        $this->app->singleton(WebhookSignature::class);
        $this->app->singleton(PaymentIntentService::class);
        $this->app->singleton(WebhookHandler::class);
    }

    public function packageBooted(): void
    {
        Gate::policy(PaymentIntent::class, PaymentIntentPolicy::class);
        Gate::policy(PaymentProviderConnection::class, PaymentProviderConnectionPolicy::class);
        Gate::policy(PaymentRefund::class, PaymentRefundPolicy::class);
        Gate::policy(PaymentReconciliation::class, PaymentReconciliationPolicy::class);
        Gate::policy(PaymentWebhookEvent::class, PaymentWebhookEventPolicy::class);

        if (class_exists(CapabilityRegistryInterface::class)) {
            $registry = $this->app->make(CapabilityRegistryInterface::class);
            PaymentCapabilities::register($registry);
        }

        if (config('filamat-iam.audit.enabled', true)) {
            PaymentRefund::observe(AuditableObserver::class);
        }

        $this->registerProviders();
    }

    protected function registerProviders(): void
    {
        $registry = $this->app->make(PaymentProviderRegistry::class);
        $providers = config('filament-payments.providers', []);

        foreach ($providers as $provider) {
            $class = $provider['class'] ?? null;
            if (! $class || ! class_exists($class)) {
                continue;
            }

            $registry->register($this->app->make($class));
        }
    }
}
