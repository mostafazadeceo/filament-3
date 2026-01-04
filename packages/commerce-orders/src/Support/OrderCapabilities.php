<?php

namespace Haida\CommerceOrders\Support;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\CommerceOrders\Policies\OrderPolicy;
use Haida\CommerceOrders\Policies\OrderRefundPolicy;
use Haida\CommerceOrders\Policies\OrderReturnPolicy;

final class OrderCapabilities
{
    private static bool $registered = false;

    public static function register(CapabilityRegistryInterface $registry): void
    {
        if (self::$registered) {
            return;
        }

        $registry->register(
            'commerce-orders',
            self::permissions(),
            [
                'commerce_orders' => true,
            ],
            [],
            [
                OrderPolicy::class,
                OrderReturnPolicy::class,
                OrderRefundPolicy::class,
            ],
            [
                'commerce_orders' => 'سفارش‌ها',
                'commerce_orders_main' => 'سفارش‌ها',
                'commerce_orders_returns' => 'مرجوعی‌ها',
                'commerce_orders_refunds' => 'بازپرداخت‌ها',
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
            'commerce.order.view',
            'commerce.order.manage',
            'commerce.order.return.view',
            'commerce.order.return.manage',
            'commerce.order.refund.view',
            'commerce.order.refund.manage',
        ];
    }
}
