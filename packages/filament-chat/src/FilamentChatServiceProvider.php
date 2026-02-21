<?php

declare(strict_types=1);

namespace Haida\FilamentChat;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\FilamentChat\Models\ChatConnection;
use Haida\FilamentChat\Models\ChatUserLink;
use Haida\FilamentChat\Policies\ChatConnectionPolicy;
use Haida\FilamentChat\Policies\ChatUserLinkPolicy;
use Haida\FilamentChat\Support\ChatCapabilities;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentChatServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-chat')
            ->hasConfigFile('filament-chat')
            ->hasTranslations()
            ->hasRoutes(['api', 'web'])
            ->hasMigrations([
                '2026_02_07_000001_create_chat_tables',
                '2026_02_07_000002_add_oidc_client_unique_to_chat_connections',
            ])
            ->runsMigrations();
    }

    public function packageBooted(): void
    {
        Gate::policy(ChatConnection::class, ChatConnectionPolicy::class);
        Gate::policy(ChatUserLink::class, ChatUserLinkPolicy::class);

        if (interface_exists(CapabilityRegistryInterface::class)) {
            $registry = $this->app->make(CapabilityRegistryInterface::class);
            ChatCapabilities::register($registry);
        }
    }
}
