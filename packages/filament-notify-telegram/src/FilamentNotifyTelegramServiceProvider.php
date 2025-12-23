<?php

namespace Haida\FilamentNotify\Telegram;

use Haida\FilamentNotify\Core\Channels\ChannelRegistry;
use Haida\FilamentNotify\Telegram\Channels\TelegramChannelDriver;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentNotifyTelegramServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-notify-telegram')
            ->hasConfigFile('filament-notify-telegram');
    }

    public function packageBooted(): void
    {
        app(ChannelRegistry::class)->register(new TelegramChannelDriver());
    }
}
