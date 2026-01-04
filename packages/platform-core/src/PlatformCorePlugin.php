<?php

namespace Haida\PlatformCore;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Haida\PlatformCore\Filament\Resources\PluginRegistryResource;
use Haida\PlatformCore\Filament\Resources\TenantPluginResource;

class PlatformCorePlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'platform-core';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            PluginRegistryResource::class,
            TenantPluginResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        // No-op.
    }
}
