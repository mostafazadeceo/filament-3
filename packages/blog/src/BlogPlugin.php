<?php

namespace Haida\Blog;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Haida\Blog\Filament\Resources\BlogCategoryResource;
use Haida\Blog\Filament\Resources\BlogPostResource;
use Haida\Blog\Filament\Resources\BlogTagResource;

class BlogPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'blog';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            BlogPostResource::class,
            BlogCategoryResource::class,
            BlogTagResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        // No-op.
    }
}
