<?php

namespace Haida\CommerceOrders;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Filamat\IamSuite\Observers\AuditableObserver;
use Haida\CommerceOrders\Models\Order;
use Haida\CommerceOrders\Models\OrderRefund;
use Haida\CommerceOrders\Models\OrderReturn;
use Haida\CommerceOrders\Policies\OrderPolicy;
use Haida\CommerceOrders\Policies\OrderRefundPolicy;
use Haida\CommerceOrders\Policies\OrderReturnPolicy;
use Haida\CommerceOrders\Services\OrderNumberGenerator;
use Haida\CommerceOrders\Services\OrderRefundService;
use Haida\CommerceOrders\Services\OrderReturnService;
use Haida\CommerceOrders\Services\OrderWorkflowService;
use Haida\CommerceOrders\Support\OrderCapabilities;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class CommerceOrdersServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('commerce-orders')
            ->hasConfigFile('commerce-orders')
            ->hasRoutes('api')
            ->hasMigrations([
                '2025_12_30_000011_create_commerce_order_tables',
                '2026_01_02_000014_create_commerce_order_return_tables',
            ])
            ->runsMigrations();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(OrderNumberGenerator::class);
        $this->app->singleton(OrderWorkflowService::class);
        $this->app->singleton(OrderReturnService::class);
        $this->app->singleton(OrderRefundService::class);
    }

    public function packageBooted(): void
    {
        Gate::policy(Order::class, OrderPolicy::class);
        Gate::policy(OrderReturn::class, OrderReturnPolicy::class);
        Gate::policy(OrderRefund::class, OrderRefundPolicy::class);

        if (class_exists(CapabilityRegistryInterface::class)) {
            $registry = $this->app->make(CapabilityRegistryInterface::class);
            OrderCapabilities::register($registry);
        }

        if (config('filamat-iam.audit.enabled', true)) {
            OrderReturn::observe(AuditableObserver::class);
            OrderRefund::observe(AuditableObserver::class);
        }
    }
}
