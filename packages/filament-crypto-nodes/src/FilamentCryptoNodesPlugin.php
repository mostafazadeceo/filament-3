<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoNodes;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Haida\FilamentCryptoNodes\Filament\Resources\CryptoNodeConnectorResource;

class FilamentCryptoNodesPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'filament-crypto-nodes';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            CryptoNodeConnectorResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        // No-op.
    }
}
