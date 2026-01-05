<?php

namespace Haida\FilamentNotify\SmsIppanel;

use Haida\FilamentNotify\Core\Channels\ChannelRegistry;
use Haida\FilamentNotify\SmsIppanel\Channels\IppanelPatternChannelDriver;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentNotifySmsIppanelServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-notify-sms-ippanel')
            ->hasConfigFile('filament-notify-sms-ippanel');
    }

    public function packageBooted(): void
    {
        app(ChannelRegistry::class)->register(new IppanelPatternChannelDriver);
    }
}
