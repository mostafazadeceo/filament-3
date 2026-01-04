<?php

namespace Haida\CommerceOrders;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Haida\CommerceOrders\Filament\Resources\CommerceOrderResource;
use Haida\CommerceOrders\Filament\Resources\CommerceOrderRefundResource;
use Haida\CommerceOrders\Filament\Resources\CommerceOrderReturnResource;

class CommerceOrdersPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'commerce-orders';
    }

    public function register(Panel $panel): void
    {
        $resources = array_filter([
            CommerceOrderResource::class,
            CommerceOrderReturnResource::class,
            CommerceOrderRefundResource::class,
        ], static fn (string $resource): bool => class_exists($resource));

        $panel->resources($resources);
    }

    public function boot(Panel $panel): void
    {
        // No-op.
    }
}
