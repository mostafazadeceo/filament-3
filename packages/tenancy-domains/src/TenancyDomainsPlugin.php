<?php

namespace Haida\TenancyDomains;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Haida\TenancyDomains\Filament\Resources\SiteDomainResource;

class TenancyDomainsPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'tenancy-domains';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            SiteDomainResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        // No-op.
    }
}
