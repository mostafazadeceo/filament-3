<?php

namespace Haida\FilamentMarketplaceConnectors\Http\Controllers\Api\V1;

use Haida\FilamentMarketplaceConnectors\Http\Resources\MarketplaceConnectorResource;
use Haida\FilamentMarketplaceConnectors\Models\MarketplaceConnector;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ConnectorController
{
    public function index(): AnonymousResourceCollection
    {
        $connectors = MarketplaceConnector::query()->latest()->paginate();

        return MarketplaceConnectorResource::collection($connectors);
    }
}
