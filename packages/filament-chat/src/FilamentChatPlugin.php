<?php

declare(strict_types=1);

namespace Haida\FilamentChat;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Haida\FilamentChat\Filament\Resources\ChatConnectionResource;
use Haida\FilamentChat\Filament\Resources\ChatUserLinkResource;

class FilamentChatPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'filament-chat';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            ChatConnectionResource::class,
            ChatUserLinkResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        // No-op.
    }
}
