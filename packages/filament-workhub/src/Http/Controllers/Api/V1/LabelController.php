<?php

namespace Haida\FilamentWorkhub\Http\Controllers\Api\V1;

use Haida\FilamentWorkhub\Http\Requests\LabelRequest;
use Haida\FilamentWorkhub\Http\Requests\StoreLabelRequest;
use Haida\FilamentWorkhub\Http\Resources\LabelResource;
use Haida\FilamentWorkhub\Models\Label;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

class LabelController extends ApiController
{
    public function index(): ResourceCollection
    {
        $this->authorize('viewAny', Label::class);

        return LabelResource::collection(Label::query()->paginate(100));
    }

    public function store(StoreLabelRequest $request): LabelResource
    {
        $this->authorize('create', Label::class);

        $label = Label::query()->create($request->validated());

        return new LabelResource($label);
    }

    public function update(LabelRequest $request, Label $label): LabelResource
    {
        $this->authorize('update', $label);

        $label->update($request->validated());

        return new LabelResource($label->refresh());
    }

    public function destroy(Label $label): JsonResponse
    {
        $this->authorize('delete', $label);

        $label->delete();

        return response()->json([], 204);
    }
}
