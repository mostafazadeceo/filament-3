<?php

namespace Haida\FilamentMarketplaceConnectors\Services;

use Haida\FilamentMarketplaceConnectors\Contracts\MarketplaceConnectorInterface;
use InvalidArgumentException;

class MarketplaceConnectorRegistry
{
    /** @var array<string, MarketplaceConnectorInterface> */
    protected array $connectors = [];

    public function register(MarketplaceConnectorInterface $connector): void
    {
        $this->connectors[$connector->key()] = $connector;
    }

    public function get(string $key): MarketplaceConnectorInterface
    {
        if (! array_key_exists($key, $this->connectors)) {
            throw new InvalidArgumentException('Unknown connector: '.$key);
        }

        return $this->connectors[$key];
    }

    /**
     * @return array<string, MarketplaceConnectorInterface>
     */
    public function all(): array
    {
        return $this->connectors;
    }
}
