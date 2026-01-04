<?php

declare(strict_types=1);

namespace Haida\FilamentProvidersEsimGo;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentProvidersEsimGoServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-providers-esim-go')
            ->hasConfigFile('filament-providers-esim-go')
            ->hasTranslations();
    }
}
