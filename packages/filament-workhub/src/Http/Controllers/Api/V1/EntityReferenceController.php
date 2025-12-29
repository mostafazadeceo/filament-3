<?php

namespace Haida\FilamentWorkhub\Http\Controllers\Api\V1;

use Haida\FilamentWorkhub\Http\Requests\EntityReferenceRequest;
use Haida\FilamentWorkhub\Http\Resources\EntityReferenceResource;
use Haida\FilamentWorkhub\Models\EntityReference;
use Haida\FilamentWorkhub\Models\WorkItem;
use Haida\FilamentWorkhub\Support\EntityReferenceRegistry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class EntityReferenceController extends BaseController
{
    public function index(Request $request, WorkItem $workItem)
    {
        $this->authorize('view', $workItem);

        $query = $workItem->links()->orderByDesc('created_at');

        return EntityReferenceResource::collection($query->paginate($request->integer('per_page', 50)));
    }

    public function store(EntityReferenceRequest $request, WorkItem $workItem)
    {
        $this->authorize('create', EntityReference::class);

        $data = $request->validated();
        $registry = app(EntityReferenceRegistry::class);
        $definition = $registry->get($data['target_type']);

        if (! $definition) {
            return response()->json(['message' => 'نوع لینک نامعتبر است.'], 422);
        }

        $modelClass = $definition['model'];
        /** @var Model $target */
        $target = $modelClass::query()->find($data['target_id']);
        if (! $target) {
            return response()->json(['message' => 'مقصد یافت نشد.'], 404);
        }

        $targetTenantId = $target->getAttribute('tenant_id');
        if ($targetTenantId && $targetTenantId !== $workItem->tenant_id) {
            return response()->json(['message' => 'مقصد در این فضای کاری نیست.'], 422);
        }

        $link = $workItem->links()->create([
            'tenant_id' => $workItem->tenant_id,
            'source_type' => $workItem::class,
            'source_id' => $workItem->getKey(),
            'target_type' => $data['target_type'],
            'target_id' => $data['target_id'],
            'relation_type' => $data['relation_type'] ?? null,
        ]);

        return new EntityReferenceResource($link);
    }

    public function destroy(EntityReference $entityReference)
    {
        $this->authorize('delete', $entityReference);

        $entityReference->delete();

        return response()->json(['status' => 'ok']);
    }
}
