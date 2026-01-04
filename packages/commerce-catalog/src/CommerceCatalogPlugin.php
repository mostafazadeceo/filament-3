<?php

namespace Haida\CommerceCatalog;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Haida\CommerceCatalog\Filament\Resources\CatalogCollectionResource;
use Haida\CommerceCatalog\Filament\Resources\CatalogProductResource;

class CommerceCatalogPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'commerce-catalog';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            CatalogProductResource::class,
            CatalogCollectionResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        // No-op.
    }
}
