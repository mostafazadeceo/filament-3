<?php

namespace Haida\CommerceCheckout\Support;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\CommerceCheckout\Policies\CartItemPolicy;
use Haida\CommerceCheckout\Policies\CartPolicy;

final class CheckoutCapabilities
{
    private static bool $registered = false;

    public static function register(CapabilityRegistryInterface $registry): void
    {
        if (self::$registered) {
            return;
        }

        $registry->register(
            'commerce-checkout',
            self::permissions(),
            [
                'commerce_checkout' => true,
            ],
            [],
            [
                CartPolicy::class,
                CartItemPolicy::class,
            ],
            [
                'commerce_checkout' => 'سبد خرید و پرداخت',
                'commerce_checkout_cart' => 'سبد خرید',
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
            'commerce.cart.view',
            'commerce.cart.manage',
            'commerce.checkout.create',
        ];
    }
}
