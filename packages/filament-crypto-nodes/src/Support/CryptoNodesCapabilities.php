<?php

namespace Haida\FilamentCryptoNodes\Support;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\FilamentCryptoNodes\Policies\CryptoNodeConnectorPolicy;

final class CryptoNodesCapabilities
{
    private static bool $registered = false;

    public static function register(CapabilityRegistryInterface $registry): void
    {
        if (self::$registered) {
            return;
        }

        $registry->register(
            'filament-crypto-nodes',
            self::permissions(),
            [
                'crypto_nodes' => true,
                'crypto.nodes' => true,
            ],
            [],
            [
                CryptoNodeConnectorPolicy::class,
            ],
            [
                'crypto_nodes' => 'نودهای رمزارز',
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
            'crypto.nodes.view',
            'crypto.nodes.manage',
        ];
    }
}
