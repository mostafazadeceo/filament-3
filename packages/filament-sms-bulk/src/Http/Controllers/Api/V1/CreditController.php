<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Http\Controllers\Api\V1;

use Haida\SmsBulk\Models\SmsBulkProviderConnection;
use Haida\SmsBulk\Services\ProviderClientFactory;

class CreditController extends ApiController
{
    public function __invoke(ProviderClientFactory $factory)
    {
        $connection = SmsBulkProviderConnection::query()->where('tenant_id', $this->tenantId())->where('status', 'active')->first();
        if (! $connection) {
            return $this->ok(['credit' => null]);
        }

        $response = $factory->make($connection)->myCredit();

        return $this->ok($response);
    }
}
