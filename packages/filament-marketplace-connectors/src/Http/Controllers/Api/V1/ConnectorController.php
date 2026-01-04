<?php

namespace Haida\FilamentMarketplaceConnectors\Http\Controllers\Api\V1;

use Haida\FilamentMarketplaceConnectors\Models\MarketplaceConnector;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Haida\FilamentMarketplaceConnectors\Http\Resources\MarketplaceConnectorResource;

class ConnectorController
{
    public function index(): AnonymousResourceCollection
    {
        $connectors = MarketplaceConnector::query()->latest()->paginate();

        return MarketplaceConnectorResource::collection($connectors);
    }
}
