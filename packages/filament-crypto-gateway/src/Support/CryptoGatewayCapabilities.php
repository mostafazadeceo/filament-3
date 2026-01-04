<?php

namespace Haida\FilamentCryptoGateway\Support;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\FilamentCryptoGateway\Policies\CryptoAiReportPolicy;
use Haida\FilamentCryptoGateway\Policies\CryptoInvoicePaymentPolicy;
use Haida\FilamentCryptoGateway\Policies\CryptoInvoicePolicy;
use Haida\FilamentCryptoGateway\Policies\CryptoPayoutPolicy;
use Haida\FilamentCryptoGateway\Policies\CryptoProviderAccountPolicy;
use Haida\FilamentCryptoGateway\Policies\CryptoReconciliationPolicy;
use Haida\FilamentCryptoGateway\Policies\CryptoWebhookCallPolicy;

final class CryptoGatewayCapabilities
{
    private static bool $registered = false;

    public static function register(CapabilityRegistryInterface $registry): void
    {
        if (self::$registered) {
            return;
        }

        $registry->register(
            'filament-crypto-gateway',
            self::permissions(),
            [
                'crypto_gateway' => true,
                'crypto.providers' => true,
                'crypto.payouts' => true,
                'crypto.webhook_replay' => true,
                'crypto.ai_auditor' => true,
            ],
            [],
            [
                CryptoProviderAccountPolicy::class,
                CryptoInvoicePolicy::class,
                CryptoInvoicePaymentPolicy::class,
                CryptoPayoutPolicy::class,
                \Haida\FilamentCryptoGateway\Policies\CryptoPayoutDestinationPolicy::class,
                CryptoWebhookCallPolicy::class,
                CryptoReconciliationPolicy::class,
                CryptoAiReportPolicy::class,
            ],
            [
                'crypto_gateway' => 'درگاه رمزارز',
                'crypto_providers' => 'اتصالات درگاه',
                'crypto_invoices' => 'فاکتورهای رمزارز',
                'crypto_payouts' => 'برداشت‌ها',
                'crypto_payout_destinations' => 'لیست سفید برداشت',
                'crypto_webhooks' => 'وبهوک‌ها',
                'crypto_reconcile' => 'آشتی‌سازی',
                'crypto_ai' => 'حسابرس هوشمند',
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
            'crypto.providers.view',
            'crypto.providers.manage',
            'crypto.invoices.view',
            'crypto.invoices.manage',
            'crypto.invoice_payments.view',
            'crypto.invoice_payments.manage',
            'crypto.payouts.view',
            'crypto.payouts.manage',
            'crypto.payouts.approve',
            'crypto.payout_destinations.view',
            'crypto.payout_destinations.manage',
            'crypto.webhooks.view',
            'crypto.webhooks.manage',
            'crypto.reconciliations.view',
            'crypto.reconciliations.manage',
            'crypto.reconcile.run',
            'crypto.ai_reports.view',
            'crypto.ai_reports.manage',
        ];
    }
}
