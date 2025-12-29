<?php

namespace Haida\FilamentWorkhub\Http\Controllers\Api\V1;

use Haida\FilamentWorkhub\Http\Requests\StoreTimeEntryRequest;
use Haida\FilamentWorkhub\Http\Resources\TimeEntryResource;
use Haida\FilamentWorkhub\Models\TimeEntry;
use Haida\FilamentWorkhub\Models\WorkItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TimeEntryController extends ApiController
{
    public function index(WorkItem $workItem): ResourceCollection
    {
        $this->authorize('view', $workItem);

        return TimeEntryResource::collection(
            $workItem->timeEntries()->latest()->paginate(50)
        );
    }

    public function store(StoreTimeEntryRequest $request, WorkItem $workItem): TimeEntryResource
    {
        $this->authorize('create', TimeEntry::class);

        $data = $request->validated();
        $data['tenant_id'] = $workItem->tenant_id;
        $data['work_item_id'] = $workItem->getKey();
        $data['user_id'] = auth()->id();

        $entry = TimeEntry::query()->create($data);

        return new TimeEntryResource($entry);
    }

    public function destroy(TimeEntry $timeEntry): JsonResponse
    {
        $this->authorize('delete', $timeEntry);

        $timeEntry->delete();

        return response()->json([], 204);
    }
}
