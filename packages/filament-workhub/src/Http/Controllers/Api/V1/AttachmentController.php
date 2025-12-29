<?php

namespace Haida\FilamentWorkhub\Http\Controllers\Api\V1;

use Haida\FilamentWorkhub\Http\Requests\StoreAttachmentRequest;
use Haida\FilamentWorkhub\Http\Resources\AttachmentResource;
use Haida\FilamentWorkhub\Models\Attachment;
use Haida\FilamentWorkhub\Models\WorkItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends ApiController
{
    public function index(WorkItem $workItem): ResourceCollection
    {
        $this->authorize('view', $workItem);

        return AttachmentResource::collection(
            $workItem->attachments()->latest()->paginate(50)
        );
    }

    public function store(StoreAttachmentRequest $request, WorkItem $workItem): AttachmentResource
    {
        $this->authorize('create', Attachment::class);

        $file = $request->file('file');
        $disk = 'public';
        $path = $file->store('workhub/attachments', $disk);

        $attachment = Attachment::query()->create([
            'tenant_id' => $workItem->tenant_id,
            'work_item_id' => $workItem->getKey(),
            'user_id' => auth()->id(),
            'disk' => $disk,
            'path' => $path,
            'filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
        ]);

        return new AttachmentResource($attachment);
    }

    public function destroy(Attachment $attachment): JsonResponse
    {
        $this->authorize('delete', $attachment);

        if ($attachment->disk && $attachment->path) {
            Storage::disk($attachment->disk)->delete($attachment->path);
        }

        $attachment->delete();

        return response()->json([], 204);
    }
}
