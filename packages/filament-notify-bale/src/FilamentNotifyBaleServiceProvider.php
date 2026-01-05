<?php

namespace Haida\FilamentNotify\Bale;

use Haida\FilamentNotify\Bale\Channels\BaleChannelDriver;
use Haida\FilamentNotify\Core\Channels\ChannelRegistry;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentNotifyBaleServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-notify-bale')
            ->hasConfigFile('filament-notify-bale');
    }

    public function packageBooted(): void
    {
        app(ChannelRegistry::class)->register(new BaleChannelDriver);
    }
}
