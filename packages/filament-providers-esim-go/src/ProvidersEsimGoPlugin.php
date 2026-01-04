<?php

declare(strict_types=1);

namespace Haida\FilamentProvidersEsimGo;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Haida\FilamentProvidersEsimGo\Resources\EsimGoCatalogueSnapshotResource;
use Haida\FilamentProvidersEsimGo\Resources\EsimGoConnectionResource;
use Haida\FilamentProvidersEsimGo\Resources\EsimGoEsimResource;
use Haida\FilamentProvidersEsimGo\Resources\EsimGoOrderResource;
use Haida\FilamentProvidersEsimGo\Resources\EsimGoProductResource;

class ProvidersEsimGoPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'providers-esim-go';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            EsimGoConnectionResource::class,
            EsimGoCatalogueSnapshotResource::class,
            EsimGoProductResource::class,
            EsimGoOrderResource::class,
            EsimGoEsimResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        // No-op.
    }
}
