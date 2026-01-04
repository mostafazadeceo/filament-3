<?php

namespace Haida\FilamentCommerceCore;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Haida\FilamentCommerceCore\Filament\Resources\CommerceBrandResource;
use Haida\FilamentCommerceCore\Filament\Resources\CommerceCategoryResource;
use Haida\FilamentCommerceCore\Filament\Resources\CommerceCustomerResource;
use Haida\FilamentCommerceCore\Filament\Resources\CommerceExceptionResource;
use Haida\FilamentCommerceCore\Filament\Resources\CommerceFraudRuleResource;
use Haida\FilamentCommerceCore\Filament\Resources\CommerceInventoryItemResource;
use Haida\FilamentCommerceCore\Filament\Resources\CommercePriceListResource;
use Haida\FilamentCommerceCore\Filament\Resources\CommerceProductResource;

class FilamentCommerceCorePlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'filament-commerce-core';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            CommerceProductResource::class,
            CommerceCategoryResource::class,
            CommerceBrandResource::class,
            CommercePriceListResource::class,
            CommerceInventoryItemResource::class,
            CommerceCustomerResource::class,
            CommerceExceptionResource::class,
            CommerceFraudRuleResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        // No-op.
    }
}
