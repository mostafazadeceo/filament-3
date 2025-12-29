<?php

namespace Haida\FilamentPettyCashIr\Http\Controllers\Api\V1;

use Haida\FilamentPettyCashIr\Http\Requests\StoreCategoryRequest;
use Haida\FilamentPettyCashIr\Http\Requests\UpdateCategoryRequest;
use Haida\FilamentPettyCashIr\Http\Resources\CategoryResource;
use Haida\FilamentPettyCashIr\Models\PettyCashCategory;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(PettyCashCategory::class, 'category');
    }

    public function index(): AnonymousResourceCollection
    {
        $categories = PettyCashCategory::query()->latest()->paginate();

        return CategoryResource::collection($categories);
    }

    public function show(PettyCashCategory $category): CategoryResource
    {
        return new CategoryResource($category);
    }

    public function store(StoreCategoryRequest $request): CategoryResource
    {
        $category = PettyCashCategory::query()->create($request->validated());

        return new CategoryResource($category);
    }

    public function update(UpdateCategoryRequest $request, PettyCashCategory $category): CategoryResource
    {
        $category->update($request->validated());

        return new CategoryResource($category->refresh());
    }

    public function destroy(PettyCashCategory $category): array
    {
        $category->delete();

        return ['status' => 'ok'];
    }
}
