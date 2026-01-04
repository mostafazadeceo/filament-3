<?php

namespace Haida\Blog\Http\Controllers\Api\V1;

use Haida\Blog\Http\Requests\StoreBlogCategoryRequest;
use Haida\Blog\Http\Requests\UpdateBlogCategoryRequest;
use Haida\Blog\Http\Resources\BlogCategoryResource;
use Haida\Blog\Models\BlogCategory;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BlogCategoryController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(BlogCategory::class, 'category');
    }

    public function index(): AnonymousResourceCollection
    {
        $categories = BlogCategory::query()->with('site')->latest()->paginate();

        return BlogCategoryResource::collection($categories);
    }

    public function show(BlogCategory $category): BlogCategoryResource
    {
        return new BlogCategoryResource($category->loadMissing('site'));
    }

    public function store(StoreBlogCategoryRequest $request): BlogCategoryResource
    {
        $data = $request->validated();
        $data['created_by_user_id'] = auth()->id();
        $data['updated_by_user_id'] = auth()->id();

        $category = BlogCategory::query()->create($data);

        return new BlogCategoryResource($category->loadMissing('site'));
    }

    public function update(UpdateBlogCategoryRequest $request, BlogCategory $category): BlogCategoryResource
    {
        $data = $request->validated();
        $data['updated_by_user_id'] = auth()->id();

        $category->update($data);

        return new BlogCategoryResource($category->refresh()->loadMissing('site'));
    }

    public function destroy(BlogCategory $category): array
    {
        $category->delete();

        return ['status' => 'ok'];
    }
}
