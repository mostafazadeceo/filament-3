<?php

namespace Haida\FilamentWorkhub\Http\Controllers\Api\V1;

use Haida\FilamentWorkhub\Http\Requests\StoreWatcherRequest;
use Haida\FilamentWorkhub\Http\Resources\WatcherResource;
use Haida\FilamentWorkhub\Models\Watcher;
use Haida\FilamentWorkhub\Models\WorkItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

class WatcherController extends ApiController
{
    public function index(WorkItem $workItem): ResourceCollection
    {
        $this->authorize('view', $workItem);

        return WatcherResource::collection(
            $workItem->watchers()->latest()->paginate(50)
        );
    }

    public function store(StoreWatcherRequest $request, WorkItem $workItem): WatcherResource
    {
        $this->authorize('create', Watcher::class);

        $watcher = Watcher::query()->firstOrCreate([
            'tenant_id' => $workItem->tenant_id,
            'work_item_id' => $workItem->getKey(),
            'user_id' => $request->validated()['user_id'],
        ]);

        return new WatcherResource($watcher);
    }

    public function destroy(Watcher $watcher): JsonResponse
    {
        $this->authorize('delete', $watcher);

        $watcher->delete();

        return response()->json([], 204);
    }
}
