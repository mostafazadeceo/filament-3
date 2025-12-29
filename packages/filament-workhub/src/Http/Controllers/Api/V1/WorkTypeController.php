<?php

namespace Haida\FilamentWorkhub\Http\Controllers\Api\V1;

use Haida\FilamentWorkhub\Http\Requests\WorkTypeRequest;
use Haida\FilamentWorkhub\Http\Resources\WorkTypeResource;
use Haida\FilamentWorkhub\Models\WorkType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

class WorkTypeController extends ApiController
{
    public function index(): ResourceCollection
    {
        $this->authorize('viewAny', WorkType::class);

        return WorkTypeResource::collection(
            WorkType::query()->orderBy('sort_order')->paginate(50)
        );
    }

    public function store(WorkTypeRequest $request): WorkTypeResource
    {
        $this->authorize('create', WorkType::class);

        $workType = WorkType::query()->create($request->validated());

        return new WorkTypeResource($workType);
    }

    public function show(WorkType $workType): WorkTypeResource
    {
        $this->authorize('view', $workType);

        return new WorkTypeResource($workType);
    }

    public function update(WorkTypeRequest $request, WorkType $workType): WorkTypeResource
    {
        $this->authorize('update', $workType);

        $workType->update($request->validated());

        return new WorkTypeResource($workType->refresh());
    }

    public function destroy(WorkType $workType): JsonResponse
    {
        $this->authorize('delete', $workType);

        $workType->delete();

        return response()->json([], 204);
    }
}
