<?php

declare(strict_types=1);

namespace Haida\FilamentMailtrap;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Haida\FilamentMailtrap\Resources\MailtrapAudienceResource;
use Haida\FilamentMailtrap\Resources\MailtrapCampaignResource;
use Haida\FilamentMailtrap\Resources\MailtrapConnectionResource;
use Haida\FilamentMailtrap\Resources\MailtrapInboxResource;
use Haida\FilamentMailtrap\Resources\MailtrapMessageResource;
use Haida\FilamentMailtrap\Resources\MailtrapOfferResource;
use Haida\FilamentMailtrap\Resources\MailtrapSendingDomainResource;
use Haida\FilamentMailtrap\Resources\MailtrapSingleSendResource;

class MailtrapPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'filament-mailtrap';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            MailtrapConnectionResource::class,
            MailtrapInboxResource::class,
            MailtrapMessageResource::class,
            MailtrapSendingDomainResource::class,
            MailtrapOfferResource::class,
            MailtrapAudienceResource::class,
            MailtrapCampaignResource::class,
            MailtrapSingleSendResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        // No-op.
    }
}
