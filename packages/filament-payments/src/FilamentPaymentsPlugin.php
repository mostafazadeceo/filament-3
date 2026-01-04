<?php

namespace Haida\FilamentPayments;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Haida\FilamentPayments\Filament\Resources\PaymentIntentResource;
use Haida\FilamentPayments\Filament\Resources\PaymentProviderConnectionResource;
use Haida\FilamentPayments\Filament\Resources\PaymentReconciliationResource;
use Haida\FilamentPayments\Filament\Resources\PaymentRefundResource;
use Haida\FilamentPayments\Filament\Resources\PaymentWebhookEventResource;

class FilamentPaymentsPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'filament-payments';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            PaymentProviderConnectionResource::class,
            PaymentIntentResource::class,
            PaymentRefundResource::class,
            PaymentReconciliationResource::class,
            PaymentWebhookEventResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        // No-op.
    }
}
