<?php

namespace Haida\PageBuilder;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Haida\PageBuilder\Filament\Resources\PageTemplateResource;

class PageBuilderPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'page-builder';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            PageTemplateResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        // No-op.
    }
}
