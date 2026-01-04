<?php

namespace Haida\PaymentsOrchestrator\Support;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\PaymentsOrchestrator\Policies\PaymentIntentPolicy;

final class PaymentCapabilities
{
    private static bool $registered = false;

    public static function register(CapabilityRegistryInterface $registry): void
    {
        if (self::$registered) {
            return;
        }

        $registry->register(
            'payments-orchestrator',
            self::permissions(),
            [
                'commerce_payments' => true,
            ],
            [],
            [
                PaymentIntentPolicy::class,
            ],
            [
                'commerce_payments' => 'پرداخت‌ها',
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
            'commerce.payment.view',
            'commerce.payment.manage',
        ];
    }
}
