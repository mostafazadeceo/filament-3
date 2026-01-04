<?php

namespace Haida\FilamentStorefrontBuilder\Support;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\FilamentStorefrontBuilder\Policies\StoreBlockPolicy;
use Haida\FilamentStorefrontBuilder\Policies\StoreMenuPolicy;
use Haida\FilamentStorefrontBuilder\Policies\StorePagePolicy;
use Haida\FilamentStorefrontBuilder\Policies\StoreRedirectPolicy;
use Haida\FilamentStorefrontBuilder\Policies\StoreThemePolicy;

final class StorefrontCapabilities
{
    private static bool $registered = false;

    public static function register(CapabilityRegistryInterface $registry): void
    {
        if (self::$registered) {
            return;
        }

        $registry->register(
            'filament-storefront-builder',
            self::permissions(),
            [
                'storebuilder' => true,
            ],
            [],
            [
                StorePagePolicy::class,
                StoreBlockPolicy::class,
                StoreMenuPolicy::class,
                StoreThemePolicy::class,
                StoreRedirectPolicy::class,
            ],
            [
                'storebuilder' => 'سازنده فروشگاه',
                'storebuilder_pages' => 'صفحات فروشگاه',
                'storebuilder_blocks' => 'بلاک‌ها',
                'storebuilder_menus' => 'منوها',
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
            'storebuilder.view',
            'storebuilder.manage',
            'storebuilder.publish',
        ];
    }
}
