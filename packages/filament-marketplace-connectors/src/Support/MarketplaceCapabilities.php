<?php

namespace Haida\FilamentMarketplaceConnectors\Support;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\FilamentMarketplaceConnectors\Policies\MarketplaceConnectorPolicy;
use Haida\FilamentMarketplaceConnectors\Policies\MarketplaceSyncJobPolicy;

final class MarketplaceCapabilities
{
    private static bool $registered = false;

    public static function register(CapabilityRegistryInterface $registry): void
    {
        if (self::$registered) {
            return;
        }

        $registry->register(
            'filament-marketplace-connectors',
            self::permissions(),
            [
                'marketplace' => true,
            ],
            [],
            [
                MarketplaceConnectorPolicy::class,
                MarketplaceSyncJobPolicy::class,
            ],
            [
                'marketplace' => 'مارکت‌پلیس',
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
            'marketplace.connectors.manage',
            'marketplace.connectors.sync',
        ];
    }
}
