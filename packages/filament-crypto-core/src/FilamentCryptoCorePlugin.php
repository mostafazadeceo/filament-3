<?php

namespace Haida\FilamentCryptoCore;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Haida\FilamentCryptoCore\Filament\Resources\CryptoAccountResource;
use Haida\FilamentCryptoCore\Filament\Resources\CryptoAddressResource;
use Haida\FilamentCryptoCore\Filament\Resources\CryptoAuditLogResource;
use Haida\FilamentCryptoCore\Filament\Resources\CryptoFeePolicyResource;
use Haida\FilamentCryptoCore\Filament\Resources\CryptoLedgerResource;
use Haida\FilamentCryptoCore\Filament\Resources\CryptoNetworkFeeResource;
use Haida\FilamentCryptoCore\Filament\Resources\CryptoRateResource;
use Haida\FilamentCryptoCore\Filament\Resources\CryptoWalletResource;

class FilamentCryptoCorePlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'filament-crypto-core';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            CryptoAccountResource::class,
            CryptoLedgerResource::class,
            CryptoWalletResource::class,
            CryptoAddressResource::class,
            CryptoRateResource::class,
            CryptoNetworkFeeResource::class,
            CryptoFeePolicyResource::class,
            CryptoAuditLogResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        // No-op.
    }
}
