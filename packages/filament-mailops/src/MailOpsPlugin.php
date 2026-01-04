<?php

declare(strict_types=1);

namespace Haida\FilamentMailOps;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Haida\FilamentMailOps\Filament\Resources\MailAliasResource;
use Haida\FilamentMailOps\Filament\Resources\MailDomainResource;
use Haida\FilamentMailOps\Filament\Resources\MailInboundMessageResource;
use Haida\FilamentMailOps\Filament\Resources\MailMailboxResource;
use Haida\FilamentMailOps\Filament\Resources\MailOutboundMessageResource;

class MailOpsPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'mailops';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            MailDomainResource::class,
            MailMailboxResource::class,
            MailAliasResource::class,
            MailOutboundMessageResource::class,
            MailInboundMessageResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        // Plugin boot hook.
    }
}
