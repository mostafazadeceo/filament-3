<?php

declare(strict_types=1);

namespace Haida\ProvidersCore;

use Filament\Panel;
use Filament\Contracts\Plugin;
use Haida\ProvidersCore\Filament\Resources\ProviderJobLogResource;

class ProvidersCorePlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'providers-core';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            ProviderJobLogResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
    }
}
