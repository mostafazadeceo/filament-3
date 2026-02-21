<?php

namespace Haida\Blog\Http\Controllers\Api\V1;

use Haida\Blog\Http\Requests\StoreBlogTagRequest;
use Haida\Blog\Http\Requests\UpdateBlogTagRequest;
use Haida\Blog\Http\Resources\BlogTagResource;
use Haida\Blog\Models\BlogTag;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BlogTagController extends ApiController
{
    public function __construct()
    {
        // API keys are used for server-to-server sync (no user session). For those requests
        // we rely on `filamat-iam.scope:*` middleware instead of per-user policies.
        $apiKeyHeader = (string) config('filamat-iam.api.api_key_header', 'X-Api-Key');
        if (! request()->header($apiKeyHeader)) {
            $this->authorizeResource(BlogTag::class, 'tag');
        }
    }

    public function index(): AnonymousResourceCollection
    {
        $tags = BlogTag::query()->with('site')->latest()->paginate();

        return BlogTagResource::collection($tags);
    }

    public function show(BlogTag $tag): BlogTagResource
    {
        return new BlogTagResource($tag->loadMissing('site'));
    }

    public function store(StoreBlogTagRequest $request): BlogTagResource
    {
        $tag = BlogTag::query()->create($request->validated());

        return new BlogTagResource($tag->loadMissing('site'));
    }

    public function update(UpdateBlogTagRequest $request, BlogTag $tag): BlogTagResource
    {
        $tag->update($request->validated());

        return new BlogTagResource($tag->refresh()->loadMissing('site'));
    }

    public function destroy(BlogTag $tag): array
    {
        $tag->delete();

        return ['status' => 'ok'];
    }
}
