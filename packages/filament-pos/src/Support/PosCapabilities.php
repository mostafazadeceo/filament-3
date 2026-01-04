<?php

namespace Haida\FilamentPos\Support;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\FilamentPos\Policies\PosCashMovementPolicy;
use Haida\FilamentPos\Policies\PosCashierSessionPolicy;
use Haida\FilamentPos\Policies\PosDevicePolicy;
use Haida\FilamentPos\Policies\PosRegisterPolicy;
use Haida\FilamentPos\Policies\PosSalePolicy;
use Haida\FilamentPos\Policies\PosStorePolicy;

final class PosCapabilities
{
    private static bool $registered = false;

    public static function register(CapabilityRegistryInterface $registry): void
    {
        if (self::$registered) {
            return;
        }

        $registry->register(
            'filament-pos',
            self::permissions(),
            [
                'pos' => true,
            ],
            [],
            [
                PosStorePolicy::class,
                PosRegisterPolicy::class,
                PosDevicePolicy::class,
                PosCashierSessionPolicy::class,
                PosCashMovementPolicy::class,
                PosSalePolicy::class,
            ],
            [
                'pos' => 'پوز',
                'pos_registers' => 'صندوق و پایانه',
                'pos_cash' => 'مدیریت نقدی',
                'pos_sales' => 'فروش پوز',
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
            'pos.view',
            'pos.use',
            'pos.manage_register',
            'pos.manage_cash',
            'pos.refund',
            'pos.void',
            'pos.override_discount',
        ];
    }
}
