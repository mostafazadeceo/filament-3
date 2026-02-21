<?php

namespace Haida\CommerceCheckout;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\CommerceCheckout\Models\Cart;
use Haida\CommerceCheckout\Models\CartItem;
use Haida\CommerceCheckout\Policies\CartItemPolicy;
use Haida\CommerceCheckout\Policies\CartPolicy;
use Haida\CommerceCheckout\Services\CartService;
use Haida\CommerceCheckout\Services\CheckoutService;
use Haida\CommerceCheckout\Services\OrderInventoryService;
use Haida\CommerceCheckout\Support\CheckoutCapabilities;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class CommerceCheckoutServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('commerce-checkout')
            ->hasConfigFile('commerce-checkout')
            ->hasRoutes('api')
            ->hasMigrations([
                '2025_12_30_000012_create_commerce_checkout_tables',
            ])
            ->runsMigrations();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(CartService::class);
        $this->app->singleton(CheckoutService::class);
        $this->app->singleton(OrderInventoryService::class);
    }

    public function packageBooted(): void
    {
        Gate::policy(Cart::class, CartPolicy::class);
        Gate::policy(CartItem::class, CartItemPolicy::class);

        if (interface_exists(CapabilityRegistryInterface::class)) {
            $registry = $this->app->make(CapabilityRegistryInterface::class);
            CheckoutCapabilities::register($registry);
        }
    }
}
