<?php

declare(strict_types=1);

namespace Haida\FilamentMailtrap;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentMailtrapServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-mailtrap')
            ->hasConfigFile('filament-mailtrap')
            ->hasTranslations();
    }
}
