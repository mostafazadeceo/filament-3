<?php

namespace Haida\FilamentWorkhub\Http\Controllers\Api\V1;

use Haida\FilamentWorkhub\Http\Requests\StoreDecisionRequest;
use Haida\FilamentWorkhub\Http\Resources\DecisionResource;
use Haida\FilamentWorkhub\Models\Decision;
use Haida\FilamentWorkhub\Models\WorkItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DecisionController extends ApiController
{
    public function index(WorkItem $workItem): ResourceCollection
    {
        $this->authorize('view', $workItem);

        return DecisionResource::collection(
            $workItem->decisions()->latest()->paginate(50)
        );
    }

    public function store(StoreDecisionRequest $request, WorkItem $workItem): DecisionResource
    {
        $this->authorize('create', Decision::class);

        $data = $request->validated();
        $data['tenant_id'] = $workItem->tenant_id;
        $data['work_item_id'] = $workItem->getKey();
        $data['user_id'] = auth()->id();

        $decision = Decision::query()->create($data);

        return new DecisionResource($decision);
    }

    public function destroy(Decision $decision): JsonResponse
    {
        $this->authorize('delete', $decision);

        $decision->delete();

        return response()->json([], 204);
    }
}
