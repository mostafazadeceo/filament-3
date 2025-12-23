<?php

namespace Haida\FilamentNotify\WhatsApp;

use Haida\FilamentNotify\Core\Channels\ChannelRegistry;
use Haida\FilamentNotify\WhatsApp\Channels\WhatsAppChannelDriver;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentNotifyWhatsAppServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-notify-whatsapp')
            ->hasConfigFile('filament-notify-whatsapp');
    }

    public function packageBooted(): void
    {
        app(ChannelRegistry::class)->register(new WhatsAppChannelDriver());
    }
}
