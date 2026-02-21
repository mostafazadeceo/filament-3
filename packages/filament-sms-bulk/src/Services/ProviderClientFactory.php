<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Services;

use Haida\SmsBulk\Clients\IppanelEdgeClient;
use Haida\SmsBulk\Contracts\ProviderClientInterface;
use Haida\SmsBulk\Models\SmsBulkProviderConnection;

class ProviderClientFactory
{
    public function make(SmsBulkProviderConnection $connection): ProviderClientInterface
    {
        return app(IppanelEdgeClient::class, ['connection' => $connection]);
    }
}
