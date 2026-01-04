<?php

namespace Haida\FilamentMarketplaceConnectors;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Haida\FilamentMarketplaceConnectors\Filament\Resources\MarketplaceConnectorResource;
use Haida\FilamentMarketplaceConnectors\Filament\Resources\MarketplaceSyncJobResource;

class FilamentMarketplaceConnectorsPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'filament-marketplace-connectors';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            MarketplaceConnectorResource::class,
            MarketplaceSyncJobResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        // No-op.
    }
}
