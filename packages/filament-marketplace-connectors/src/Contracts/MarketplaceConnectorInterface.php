<?php

namespace Haida\FilamentMarketplaceConnectors\Contracts;

use Haida\FilamentMarketplaceConnectors\Models\MarketplaceConnector;
use Haida\FilamentMarketplaceConnectors\Models\MarketplaceToken;

interface MarketplaceConnectorInterface
{
    public function key(): string;

    /**
     * @param  array<string, mixed>  $payload
     */
    public function connect(MarketplaceConnector $connector, array $payload = []): MarketplaceToken;

    public function refreshToken(MarketplaceToken $token): MarketplaceToken;

    /**
     * @return array<string, mixed>
     */
    public function syncCatalog(MarketplaceConnector $connector): array;

    /**
     * @return array<string, mixed>
     */
    public function syncInventory(MarketplaceConnector $connector): array;

    /**
     * @return array<string, mixed>
     */
    public function syncOrders(MarketplaceConnector $connector): array;
}
