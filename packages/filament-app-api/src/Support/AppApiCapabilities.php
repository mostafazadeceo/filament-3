<?php

declare(strict_types=1);

namespace Haida\FilamentAppApi\Support;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;

final class AppApiCapabilities
{
    private static bool $registered = false;

    public static function register(CapabilityRegistryInterface $registry): void
    {
        if (self::$registered) {
            return;
        }

        $registry->register(
            'filament-app-api',
            self::permissions(),
            [
                'app' => true,
            ],
            [],
            [],
            [
                'app' => 'اپلیکیشن‌ها',
                'app_sync' => 'همگام‌سازی اپ‌ها',
                'app_devices' => 'دستگاه‌ها',
                'app_support' => 'پشتیبانی اپ‌ها',
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
            'app.view',
            'app.config.view',
            'app.sync',
            'app.device.manage',
            'app.notification.view',
            'app.notification.manage',
            'app.tenant.view',
            'app.tenant.switch',
            'app.realtime.signal',
            'support.ticket.view',
            'support.ticket.manage',
            'support.message.view',
            'support.message.manage',
            'support.attachment.manage',
        ];
    }
}
