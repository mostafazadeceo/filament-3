<?php

namespace Haida\FilamentCryptoGateway;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Haida\FilamentCryptoGateway\Filament\Resources\CryptoAiReportResource;
use Haida\FilamentCryptoGateway\Filament\Resources\CryptoInvoicePaymentResource;
use Haida\FilamentCryptoGateway\Filament\Resources\CryptoInvoiceResource;
use Haida\FilamentCryptoGateway\Filament\Resources\CryptoPayoutDestinationResource;
use Haida\FilamentCryptoGateway\Filament\Resources\CryptoPayoutResource;
use Haida\FilamentCryptoGateway\Filament\Resources\CryptoProviderAccountResource;
use Haida\FilamentCryptoGateway\Filament\Resources\CryptoReconciliationResource;
use Haida\FilamentCryptoGateway\Filament\Resources\CryptoWebhookCallResource;

class FilamentCryptoGatewayPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'filament-crypto-gateway';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            CryptoProviderAccountResource::class,
            CryptoInvoiceResource::class,
            CryptoInvoicePaymentResource::class,
            CryptoPayoutResource::class,
            CryptoPayoutDestinationResource::class,
            CryptoWebhookCallResource::class,
            CryptoReconciliationResource::class,
            CryptoAiReportResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        // No-op.
    }
}
