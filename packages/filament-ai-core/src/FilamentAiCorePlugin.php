<?php

namespace Haida\FilamentAiCore;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Haida\FilamentAiCore\Filament\Resources\AiPolicyResource;
use Haida\FilamentAiCore\Filament\Resources\AiRequestResource;

class FilamentAiCorePlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'filament-ai-core';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            AiPolicyResource::class,
            AiRequestResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        // No-op for now.
    }
}
