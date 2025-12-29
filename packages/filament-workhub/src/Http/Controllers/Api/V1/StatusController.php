<?php

namespace Haida\FilamentWorkhub\Http\Controllers\Api\V1;

use Haida\FilamentWorkhub\Http\Requests\StoreStatusRequest;
use Haida\FilamentWorkhub\Http\Requests\UpdateStatusRequest;
use Haida\FilamentWorkhub\Http\Resources\StatusResource;
use Haida\FilamentWorkhub\Models\Status;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

class StatusController extends ApiController
{
    public function index(): ResourceCollection
    {
        $this->authorize('viewAny', Status::class);

        return StatusResource::collection(Status::query()->paginate(100));
    }

    public function store(StoreStatusRequest $request): StatusResource
    {
        $this->authorize('create', Status::class);

        $status = Status::query()->create($request->validated());

        return new StatusResource($status);
    }

    public function show(Status $status): StatusResource
    {
        $this->authorize('view', $status);

        return new StatusResource($status);
    }

    public function update(UpdateStatusRequest $request, Status $status): StatusResource
    {
        $this->authorize('update', $status);

        $status->update($request->validated());

        return new StatusResource($status->refresh());
    }

    public function destroy(Status $status): JsonResponse
    {
        $this->authorize('delete', $status);

        $status->delete();

        return response()->json([], 204);
    }
}
