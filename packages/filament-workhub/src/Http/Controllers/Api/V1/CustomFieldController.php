<?php

namespace Haida\FilamentWorkhub\Http\Controllers\Api\V1;

use Haida\FilamentWorkhub\Http\Requests\CustomFieldRequest;
use Haida\FilamentWorkhub\Http\Resources\CustomFieldResource;
use Haida\FilamentWorkhub\Models\CustomField;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CustomFieldController extends ApiController
{
    public function index(): ResourceCollection
    {
        $this->authorize('viewAny', CustomField::class);

        return CustomFieldResource::collection(
            CustomField::query()->orderBy('sort_order')->paginate(50)
        );
    }

    public function store(CustomFieldRequest $request): CustomFieldResource
    {
        $this->authorize('create', CustomField::class);

        $field = CustomField::query()->create($request->validated());

        return new CustomFieldResource($field);
    }

    public function show(CustomField $customField): CustomFieldResource
    {
        $this->authorize('view', $customField);

        return new CustomFieldResource($customField);
    }

    public function update(CustomFieldRequest $request, CustomField $customField): CustomFieldResource
    {
        $this->authorize('update', $customField);

        $customField->update($request->validated());

        return new CustomFieldResource($customField->refresh());
    }

    public function destroy(CustomField $customField): JsonResponse
    {
        $this->authorize('delete', $customField);

        $customField->delete();

        return response()->json([], 204);
    }
}
