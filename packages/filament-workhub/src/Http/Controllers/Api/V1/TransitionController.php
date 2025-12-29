<?php

namespace Haida\FilamentWorkhub\Http\Controllers\Api\V1;

use Haida\FilamentWorkhub\Http\Requests\StoreTransitionRequest;
use Haida\FilamentWorkhub\Http\Requests\UpdateTransitionRequest;
use Haida\FilamentWorkhub\Http\Resources\TransitionResource;
use Haida\FilamentWorkhub\Models\Transition;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TransitionController extends ApiController
{
    public function index(): ResourceCollection
    {
        $this->authorize('viewAny', Transition::class);

        return TransitionResource::collection(Transition::query()->paginate(100));
    }

    public function store(StoreTransitionRequest $request): TransitionResource
    {
        $this->authorize('create', Transition::class);

        $transition = Transition::query()->create($request->validated());

        return new TransitionResource($transition);
    }

    public function show(Transition $transition): TransitionResource
    {
        $this->authorize('view', $transition);

        return new TransitionResource($transition);
    }

    public function update(UpdateTransitionRequest $request, Transition $transition): TransitionResource
    {
        $this->authorize('update', $transition);

        $transition->update($request->validated());

        return new TransitionResource($transition->refresh());
    }

    public function destroy(Transition $transition): JsonResponse
    {
        $this->authorize('delete', $transition);

        $transition->delete();

        return response()->json([], 204);
    }
}
