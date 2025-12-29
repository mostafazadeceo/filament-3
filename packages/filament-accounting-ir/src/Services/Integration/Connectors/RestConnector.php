<?php

namespace Vendor\FilamentAccountingIr\Services\Integration\Connectors;

use Vendor\FilamentAccountingIr\Models\IntegrationConnector as ConnectorModel;
use Vendor\FilamentAccountingIr\Services\Integration\Contracts\IntegrationConnector;
use Vendor\FilamentAccountingIr\Services\Integration\DTOs\IntegrationResult;

class RestConnector implements IntegrationConnector
{
    public function run(ConnectorModel $connector): IntegrationResult
    {
        return new IntegrationResult(
            success: true,
            summary: [
                'message' => 'REST connector executed (stub).',
            ],
            logs: [],
        );
    }
}
