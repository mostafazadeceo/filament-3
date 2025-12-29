<?php

namespace Haida\FilamentWorkhub\Http\Controllers\Api\V1;

use Haida\FilamentWorkhub\Http\Requests\StoreWorkflowRequest;
use Haida\FilamentWorkhub\Http\Requests\UpdateWorkflowRequest;
use Haida\FilamentWorkhub\Http\Resources\WorkflowResource;
use Haida\FilamentWorkhub\Models\Workflow;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

class WorkflowController extends ApiController
{
    public function index(): ResourceCollection
    {
        $this->authorize('viewAny', Workflow::class);

        $workflows = Workflow::query()->paginate(50);

        return WorkflowResource::collection($workflows);
    }

    public function store(StoreWorkflowRequest $request): WorkflowResource
    {
        $this->authorize('create', Workflow::class);

        $workflow = Workflow::query()->create($request->validated());

        return new WorkflowResource($workflow);
    }

    public function show(Workflow $workflow): WorkflowResource
    {
        $this->authorize('view', $workflow);

        return new WorkflowResource($workflow);
    }

    public function update(UpdateWorkflowRequest $request, Workflow $workflow): WorkflowResource
    {
        $this->authorize('update', $workflow);

        $workflow->update($request->validated());

        return new WorkflowResource($workflow->refresh());
    }

    public function destroy(Workflow $workflow): JsonResponse
    {
        $this->authorize('delete', $workflow);

        $workflow->delete();

        return response()->json([], 204);
    }
}
