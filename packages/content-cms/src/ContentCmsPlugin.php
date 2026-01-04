<?php

namespace Haida\ContentCms;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Haida\ContentCms\Filament\Resources\CmsPageResource;

class ContentCmsPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'content-cms';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            CmsPageResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        // No-op.
    }
}
