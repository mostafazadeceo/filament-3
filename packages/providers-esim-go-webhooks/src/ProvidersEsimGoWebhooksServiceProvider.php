<?php

declare(strict_types=1);

namespace Haida\ProvidersEsimGoWebhooks;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ProvidersEsimGoWebhooksServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('providers-esim-go-webhooks')
            ->hasConfigFile('providers-esim-go-webhooks')
            ->hasTranslations()
            ->hasRoutes('api');
    }
}
