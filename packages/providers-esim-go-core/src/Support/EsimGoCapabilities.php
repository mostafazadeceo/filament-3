<?php

declare(strict_types=1);

namespace Haida\ProvidersEsimGoCore\Support;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\ProvidersEsimGoCore\Policies\EsimGoCallbackPolicy;
use Haida\ProvidersEsimGoCore\Policies\EsimGoCatalogueSnapshotPolicy;
use Haida\ProvidersEsimGoCore\Policies\EsimGoConnectionPolicy;
use Haida\ProvidersEsimGoCore\Policies\EsimGoEsimPolicy;
use Haida\ProvidersEsimGoCore\Policies\EsimGoInventoryUsagePolicy;
use Haida\ProvidersEsimGoCore\Policies\EsimGoOrderPolicy;
use Haida\ProvidersEsimGoCore\Policies\EsimGoProductPolicy;

final class EsimGoCapabilities
{
    private static bool $registered = false;

    public static function register(CapabilityRegistryInterface $registry): void
    {
        if (self::$registered) {
            return;
        }

        $registry->register(
            'providers-esim-go',
            self::permissions(),
            [
                'providers_esim_go' => true,
            ],
            [],
            [
                EsimGoConnectionPolicy::class,
                EsimGoCatalogueSnapshotPolicy::class,
                EsimGoProductPolicy::class,
                EsimGoOrderPolicy::class,
                EsimGoEsimPolicy::class,
                EsimGoCallbackPolicy::class,
                EsimGoInventoryUsagePolicy::class,
            ],
            [
                'providers_esim_go' => 'Provider eSIM Go',
                'providers_esim_go_connections' => 'اتصال eSIM Go',
                'providers_esim_go_catalogue' => 'کاتالوگ eSIM Go',
                'providers_esim_go_orders' => 'سفارش‌های eSIM Go',
            ]
        );

        self::$registered = true;
    }

    /**
     * @return array<int, string>
     */
    public static function permissions(): array
    {
        return [
            'esim_go.connection.view',
            'esim_go.connection.manage',
            'esim_go.catalogue.view',
            'esim_go.catalogue.sync',
            'esim_go.product.view',
            'esim_go.product.manage',
            'esim_go.order.view',
            'esim_go.order.manage',
            'esim_go.fulfillment.view',
            'esim_go.webhook.view',
            'esim_go.inventory.view',
            'esim_go.inventory.refund',
        ];
    }
}
