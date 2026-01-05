<?php

declare(strict_types=1);

namespace Haida\FilamentNotify\Mailtrap;

use Haida\FilamentNotify\Core\Channels\ChannelRegistry;
use Haida\FilamentNotify\Mailtrap\Channels\MailtrapChannelDriver;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentNotifyMailtrapServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-notify-mailtrap')
            ->hasConfigFile('filament-notify-mailtrap')
            ->hasTranslations();
    }

    public function packageBooted(): void
    {
        app(ChannelRegistry::class)->register(new MailtrapChannelDriver);
    }
}
