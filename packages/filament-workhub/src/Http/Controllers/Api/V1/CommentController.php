<?php

namespace Haida\FilamentWorkhub\Http\Controllers\Api\V1;

use Haida\FilamentWorkhub\Http\Requests\StoreCommentRequest;
use Haida\FilamentWorkhub\Http\Resources\CommentResource;
use Haida\FilamentWorkhub\Models\Comment;
use Haida\FilamentWorkhub\Models\WorkItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CommentController extends ApiController
{
    public function index(WorkItem $workItem): ResourceCollection
    {
        $this->authorize('view', $workItem);

        return CommentResource::collection(
            $workItem->comments()->latest()->paginate(50)
        );
    }

    public function store(StoreCommentRequest $request, WorkItem $workItem): CommentResource
    {
        $this->authorize('create', Comment::class);

        $comment = Comment::query()->create([
            'tenant_id' => $workItem->tenant_id,
            'work_item_id' => $workItem->getKey(),
            'user_id' => auth()->id(),
            'body' => $request->validated()['body'],
            'is_internal' => (bool) ($request->validated()['is_internal'] ?? false),
        ]);

        return new CommentResource($comment);
    }

    public function destroy(Comment $comment): JsonResponse
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return response()->json([], 204);
    }
}
