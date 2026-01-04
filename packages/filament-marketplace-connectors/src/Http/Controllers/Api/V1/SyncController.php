<?php

namespace Haida\FilamentMarketplaceConnectors\Http\Controllers\Api\V1;

use Haida\FilamentMarketplaceConnectors\Http\Requests\StoreSyncJobRequest;
use Haida\FilamentMarketplaceConnectors\Jobs\SyncConnectorJob;
use Haida\FilamentMarketplaceConnectors\Models\MarketplaceConnector;
use Haida\FilamentMarketplaceConnectors\Models\MarketplaceSyncJob;
use Illuminate\Http\JsonResponse;

class SyncController
{
    public function store(StoreSyncJobRequest $request, MarketplaceConnector $connector): JsonResponse
    {
        $data = $request->validated();

        $job = MarketplaceSyncJob::query()->create([
            'tenant_id' => $connector->tenant_id,
            'connector_id' => $connector->getKey(),
            'job_type' => $data['job_type'],
            'status' => 'pending',
        ]);

        SyncConnectorJob::dispatch($job);

        return response()->json([
            'job_id' => $job->getKey(),
            'status' => $job->status,
        ]);
    }
}
