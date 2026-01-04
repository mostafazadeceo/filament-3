<?php

declare(strict_types=1);

namespace Haida\ProvidersEsimGoCommerce;

use Haida\CommerceOrders\Events\OrderPaid;
use Haida\ProvidersEsimGoCommerce\Listeners\ApplyEsimGoFulfillment;
use Haida\ProvidersEsimGoCommerce\Listeners\CreateEsimGoOrderOnPayment;
use Haida\ProvidersEsimGoCommerce\Listeners\SyncEsimGoCatalogueToCommerce;
use Haida\ProvidersEsimGoCommerce\Services\EsimGoCommerceService;
use Haida\ProvidersEsimGoCore\Events\EsimGoCatalogueSynced;
use Haida\ProvidersEsimGoCore\Events\EsimGoOrderReady;
use Illuminate\Support\Facades\Event;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ProvidersEsimGoCommerceServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('providers-esim-go-commerce')
            ->hasConfigFile('providers-esim-go-commerce')
            ->hasTranslations();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(EsimGoCommerceService::class);
    }

    public function packageBooted(): void
    {
        Event::listen(OrderPaid::class, CreateEsimGoOrderOnPayment::class);
        Event::listen(EsimGoCatalogueSynced::class, SyncEsimGoCatalogueToCommerce::class);
        Event::listen(EsimGoOrderReady::class, ApplyEsimGoFulfillment::class);
    }
}
