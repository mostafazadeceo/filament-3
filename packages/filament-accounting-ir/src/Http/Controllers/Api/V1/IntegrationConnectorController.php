<?php

namespace Vendor\FilamentAccountingIr\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentAccountingIr\Http\Controllers\Controller;
use Vendor\FilamentAccountingIr\Http\Requests\StoreIntegrationConnectorRequest;
use Vendor\FilamentAccountingIr\Http\Requests\UpdateIntegrationConnectorRequest;
use Vendor\FilamentAccountingIr\Http\Resources\IntegrationConnectorResource;
use Vendor\FilamentAccountingIr\Jobs\RunIntegrationJob;
use Vendor\FilamentAccountingIr\Models\IntegrationConnector;

class IntegrationConnectorController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $items = IntegrationConnector::query()->latest()->paginate();

        return IntegrationConnectorResource::collection($items);
    }

    public function show(IntegrationConnector $integration_connector): IntegrationConnectorResource
    {
        return new IntegrationConnectorResource($integration_connector);
    }

    public function store(StoreIntegrationConnectorRequest $request): IntegrationConnectorResource
    {
        $item = IntegrationConnector::query()->create($request->validated());

        return new IntegrationConnectorResource($item);
    }

    public function update(UpdateIntegrationConnectorRequest $request, IntegrationConnector $integration_connector): IntegrationConnectorResource
    {
        $integration_connector->update($request->validated());

        return new IntegrationConnectorResource($integration_connector);
    }

    public function destroy(IntegrationConnector $integration_connector): array
    {
        $integration_connector->delete();

        return ['status' => 'ok'];
    }

    public function run(IntegrationConnector $integration_connector): array
    {
        RunIntegrationJob::dispatch($integration_connector->getKey());

        return ['status' => 'queued'];
    }
}
