<?php

namespace Haida\FilamentWorkhub\Http\Controllers\Api\V1;

use Haida\FilamentWorkhub\Http\Requests\StoreLinkRequest;
use Haida\FilamentWorkhub\Http\Resources\EntityReferenceResource;
use Haida\FilamentWorkhub\Models\EntityReference;
use Haida\FilamentWorkhub\Models\WorkItem;
use Haida\FilamentWorkhub\Support\EntityReferenceRegistry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Schema;

class LinkController extends ApiController
{
    public function index(WorkItem $workItem): ResourceCollection
    {
        $this->authorize('view', $workItem);

        $links = EntityReference::query()
            ->where('source_type', WorkItem::class)
            ->where('source_id', $workItem->getKey())
            ->latest()
            ->paginate(50);

        return EntityReferenceResource::collection($links);
    }

    public function store(StoreLinkRequest $request, WorkItem $workItem): EntityReferenceResource|JsonResponse
    {
        $this->authorize('create', EntityReference::class);

        $data = $request->validated();
        $registry = app(EntityReferenceRegistry::class);
        $workItem->loadMissing('project');

        if ($workItem->project && ! $workItem->project->allowsLinkType($data['target_type'])) {
            return response()->json(['message' => 'نوع لینک در این پروژه مجاز نیست.'], 422);
        }

        $definition = $registry->get($data['target_type']);
        if (! $definition) {
            return response()->json(['message' => 'نوع لینک معتبر نیست.'], 422);
        }

        $targetClass = $definition['model'] ?? $data['target_type'];
        if (! class_exists($targetClass)) {
            return response()->json(['message' => 'کلاس هدف یافت نشد.'], 422);
        }

        /** @var Model|null $target */
        $target = $targetClass::query()->find($data['target_id']);
        if (! $target) {
            return response()->json(['message' => 'موجودیت هدف یافت نشد.'], 404);
        }

        if (Schema::hasColumn($target->getTable(), 'tenant_id') && (int) $target->getAttribute('tenant_id') !== (int) $workItem->tenant_id) {
            return response()->json(['message' => 'موجودیت هدف در این فضای کاری نیست.'], 422);
        }

        $link = EntityReference::query()->firstOrCreate([
            'tenant_id' => $workItem->tenant_id,
            'source_type' => WorkItem::class,
            'source_id' => $workItem->getKey(),
            'target_type' => $targetClass,
            'target_id' => $target->getKey(),
            'relation_type' => $data['relation_type'] ?? null,
        ]);

        return new EntityReferenceResource($link);
    }

    public function destroy(EntityReference $link): JsonResponse
    {
        $this->authorize('delete', $link);

        $link->delete();

        return response()->json([], 204);
    }
}
