<?php

namespace Vendor\FilamentAccountingIr\Services\Integration\Contracts;

use Vendor\FilamentAccountingIr\Models\IntegrationConnector as ConnectorModel;
use Vendor\FilamentAccountingIr\Services\Integration\DTOs\IntegrationResult;

interface IntegrationConnector
{
    public function run(ConnectorModel $connector): IntegrationResult;
}
