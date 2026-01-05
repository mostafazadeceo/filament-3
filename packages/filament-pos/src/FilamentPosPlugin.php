<?php

namespace Haida\FilamentPos;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Haida\FilamentPos\Filament\Resources\PosCashierSessionResource;
use Haida\FilamentPos\Filament\Resources\PosCashMovementResource;
use Haida\FilamentPos\Filament\Resources\PosDeviceResource;
use Haida\FilamentPos\Filament\Resources\PosRegisterResource;
use Haida\FilamentPos\Filament\Resources\PosSaleResource;
use Haida\FilamentPos\Filament\Resources\PosStoreResource;

class FilamentPosPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'filament-pos';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            PosStoreResource::class,
            PosRegisterResource::class,
            PosDeviceResource::class,
            PosCashierSessionResource::class,
            PosCashMovementResource::class,
            PosSaleResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        // No-op.
    }
}
