<?php

namespace Haida\Blog\Http\Controllers\Api\V1;

use Haida\Blog\Http\Requests\StoreBlogPostRequest;
use Haida\Blog\Http\Requests\UpdateBlogPostRequest;
use Haida\Blog\Http\Resources\BlogPostResource;
use Haida\Blog\Models\BlogPost;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BlogPostController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(BlogPost::class, 'post');
    }

    public function index(): AnonymousResourceCollection
    {
        $posts = BlogPost::query()
            ->with(['category', 'tags', 'site'])
            ->latest()
            ->paginate();

        return BlogPostResource::collection($posts);
    }

    public function show(BlogPost $post): BlogPostResource
    {
        return new BlogPostResource($post->loadMissing(['category', 'tags', 'site']));
    }

    public function store(StoreBlogPostRequest $request): BlogPostResource
    {
        $data = $request->validated();
        $data['created_by_user_id'] = auth()->id();
        $data['updated_by_user_id'] = auth()->id();

        $post = BlogPost::query()->create($data);
        if (array_key_exists('tags', $data)) {
            $post->tags()->sync($data['tags']);
        }

        return new BlogPostResource($post->loadMissing(['category', 'tags', 'site']));
    }

    public function update(UpdateBlogPostRequest $request, BlogPost $post): BlogPostResource
    {
        $data = $request->validated();
        $data['updated_by_user_id'] = auth()->id();

        $post->update($data);
        if (array_key_exists('tags', $data)) {
            $post->tags()->sync($data['tags']);
        }

        return new BlogPostResource($post->refresh()->loadMissing(['category', 'tags', 'site']));
    }

    public function destroy(BlogPost $post): array
    {
        $post->delete();

        return ['status' => 'ok'];
    }
}
