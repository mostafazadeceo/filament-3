<?php

namespace Haida\CommerceCatalog\Support;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\CommerceCatalog\Policies\CatalogCollectionPolicy;
use Haida\CommerceCatalog\Policies\CatalogProductPolicy;

final class CatalogCapabilities
{
    private static bool $registered = false;

    public static function register(CapabilityRegistryInterface $registry): void
    {
        if (self::$registered) {
            return;
        }

        $registry->register(
            'commerce-catalog',
            self::permissions(),
            [
                'commerce_catalog' => true,
            ],
            [],
            [
                CatalogProductPolicy::class,
                CatalogCollectionPolicy::class,
            ],
            [
                'commerce_catalog' => 'کاتالوگ فروش',
                'commerce_catalog_products' => 'محصولات',
                'commerce_catalog_collections' => 'مجموعه ها',
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
            'catalog.product.view',
            'catalog.product.manage',
            'catalog.collection.manage',
        ];
    }
}
