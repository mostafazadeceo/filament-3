<?php

namespace Haida\FilamentMarketplaceConnectors\Connectors;

use Haida\FilamentMarketplaceConnectors\Contracts\MarketplaceConnectorInterface;
use Haida\FilamentMarketplaceConnectors\Models\MarketplaceConnector;
use Haida\FilamentMarketplaceConnectors\Models\MarketplaceToken;

class EbaySellConnector implements MarketplaceConnectorInterface
{
    public function key(): string
    {
        return 'ebay';
    }

    public function connect(MarketplaceConnector $connector, array $payload = []): MarketplaceToken
    {
        return MarketplaceToken::query()->create([
            'tenant_id' => $connector->tenant_id,
            'connector_id' => $connector->getKey(),
            'access_token' => $payload['access_token'] ?? null,
            'refresh_token' => $payload['refresh_token'] ?? null,
            'expires_at' => $payload['expires_at'] ?? null,
            'scopes' => $payload['scopes'] ?? null,
        ]);
    }

    public function refreshToken(MarketplaceToken $token): MarketplaceToken
    {
        return $token;
    }

    public function syncCatalog(MarketplaceConnector $connector): array
    {
        return ['status' => 'stub', 'provider' => 'ebay'];
    }

    public function syncInventory(MarketplaceConnector $connector): array
    {
        return ['status' => 'stub', 'provider' => 'ebay'];
    }

    public function syncOrders(MarketplaceConnector $connector): array
    {
        return ['status' => 'stub', 'provider' => 'ebay'];
    }
}
