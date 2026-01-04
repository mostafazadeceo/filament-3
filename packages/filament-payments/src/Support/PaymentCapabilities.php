<?php

namespace Haida\FilamentPayments\Support;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\FilamentPayments\Policies\PaymentIntentPolicy;
use Haida\FilamentPayments\Policies\PaymentProviderConnectionPolicy;
use Haida\FilamentPayments\Policies\PaymentReconciliationPolicy;
use Haida\FilamentPayments\Policies\PaymentRefundPolicy;
use Haida\FilamentPayments\Policies\PaymentWebhookEventPolicy;

final class PaymentCapabilities
{
    private static bool $registered = false;

    public static function register(CapabilityRegistryInterface $registry): void
    {
        if (self::$registered) {
            return;
        }

        $registry->register(
            'filament-payments',
            self::permissions(),
            [
                'payments' => true,
            ],
            [],
            [
                PaymentIntentPolicy::class,
                PaymentProviderConnectionPolicy::class,
                PaymentRefundPolicy::class,
                PaymentReconciliationPolicy::class,
                PaymentWebhookEventPolicy::class,
            ],
            [
                'payments' => 'پرداخت‌ها',
                'payments_webhooks' => 'وبهوک‌های پرداخت',
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
            'payments.view',
            'payments.manage',
            'payments.webhooks.view',
            'payments.webhooks.manage',
        ];
    }
}
