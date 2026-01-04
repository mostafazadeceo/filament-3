<?php

namespace Haida\FilamentStorefrontBuilder;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Haida\FilamentStorefrontBuilder\Filament\Resources\StoreBlockResource;
use Haida\FilamentStorefrontBuilder\Filament\Resources\StoreMenuResource;
use Haida\FilamentStorefrontBuilder\Filament\Resources\StorePageResource;
use Haida\FilamentStorefrontBuilder\Filament\Resources\StoreRedirectResource;
use Haida\FilamentStorefrontBuilder\Filament\Resources\StoreThemeResource;

class FilamentStorefrontBuilderPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'filament-storefront-builder';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            StorePageResource::class,
            StoreBlockResource::class,
            StoreMenuResource::class,
            StoreThemeResource::class,
            StoreRedirectResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        // No-op.
    }
}
