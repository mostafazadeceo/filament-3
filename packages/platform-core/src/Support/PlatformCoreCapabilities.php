<?php

namespace Haida\PlatformCore\Support;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\PlatformCore\Policies\PluginRegistryPolicy;
use Haida\PlatformCore\Policies\TenantPluginPolicy;

final class PlatformCoreCapabilities
{
    private static bool $registered = false;

    public static function register(CapabilityRegistryInterface $registry): void
    {
        if (self::$registered) {
            return;
        }

        $registry->register(
            'platform-core',
            self::permissions(),
            [
                'platform_core' => true,
            ],
            [],
            [
                PluginRegistryPolicy::class,
                TenantPluginPolicy::class,
            ],
            [
                'platform_core' => 'هسته پلتفرم',
                'platform_core_plugins' => 'افزونه‌ها',
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
            'platform.plugins.view',
            'platform.plugins.manage',
        ];
    }
}
