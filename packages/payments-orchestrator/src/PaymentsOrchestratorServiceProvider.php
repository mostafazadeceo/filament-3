<?php

namespace Haida\PaymentsOrchestrator;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\PaymentsOrchestrator\Models\PaymentIntent;
use Haida\PaymentsOrchestrator\Policies\PaymentIntentPolicy;
use Haida\PaymentsOrchestrator\Services\GatewayRegistry;
use Haida\PaymentsOrchestrator\Services\PaymentIntentService;
use Haida\PaymentsOrchestrator\Services\WebhookHandler;
use Haida\PaymentsOrchestrator\Support\PaymentCapabilities;
use Haida\PaymentsOrchestrator\Support\WebhookSignature;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class PaymentsOrchestratorServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('payments-orchestrator')
            ->hasConfigFile('payments-orchestrator')
            ->hasRoutes('api')
            ->hasMigrations([
                '2025_12_30_000013_create_payments_orchestrator_tables',
            ])
            ->runsMigrations();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(GatewayRegistry::class);
        $this->app->singleton(WebhookSignature::class);
        $this->app->singleton(PaymentIntentService::class);
        $this->app->singleton(WebhookHandler::class);
    }

    public function packageBooted(): void
    {
        Gate::policy(PaymentIntent::class, PaymentIntentPolicy::class);

        if (class_exists(CapabilityRegistryInterface::class)) {
            $registry = $this->app->make(CapabilityRegistryInterface::class);
            PaymentCapabilities::register($registry);
        }
    }
}
