<?php

namespace Haida\SiteBuilderCore\Support;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\SiteBuilderCore\Policies\SitePolicy;

final class SiteBuilderCapabilities
{
    private static bool $registered = false;

    public static function register(CapabilityRegistryInterface $registry): void
    {
        if (self::$registered) {
            return;
        }

        $registry->register(
            'site-builder-core',
            self::permissions(),
            [
                'site_builder' => true,
            ],
            [],
            [
                SitePolicy::class,
            ],
            [
                'site_builder' => 'سایت ساز',
                'site_builder_sites' => 'سایت ها',
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
            'site_builder.site.view',
            'site_builder.site.manage',
        ];
    }
}
