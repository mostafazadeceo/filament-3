<?php

namespace Haida\SiteBuilderCore;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Haida\SiteBuilderCore\Filament\Resources\SiteResource;

class SiteBuilderCorePlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'site-builder-core';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            SiteResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        // No-op.
    }
}
