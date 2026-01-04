<?php

namespace Haida\FilamentWorkhub\Http\Controllers\Api\V1;

use Haida\FilamentWorkhub\Http\Requests\StoreWorkItemRequest;
use Haida\FilamentWorkhub\Http\Requests\UpdateWorkItemRequest;
use Haida\FilamentWorkhub\Http\Resources\WorkItemResource;
use Haida\FilamentWorkhub\Models\Label;
use Haida\FilamentWorkhub\Models\WorkItem;
use Haida\FilamentWorkhub\Services\CustomFieldManager;
use Haida\FilamentWorkhub\Services\WorkflowTransitionService;
use Haida\FilamentWorkhub\Services\WorkItemCreator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class WorkItemController extends ApiController
{
    public function index(Request $request): ResourceCollection
    {
        $this->authorize('viewAny', WorkItem::class);

        $query = WorkItem::query()->with(['labels', 'customFieldValues.field']);

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->integer('project_id'));
        }

        if ($request->filled('status_id')) {
            $query->where('status_id', $request->integer('status_id'));
        }

        if ($request->filled('assignee_id')) {
            $query->where('assignee_id', $request->integer('assignee_id'));
        }

        $items = $query->paginate(50);

        return WorkItemResource::collection($items);
    }

    public function store(StoreWorkItemRequest $request): WorkItemResource
    {
        $this->authorize('create', WorkItem::class);

        $data = $request->validated();
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();
        $customFields = (array) ($data['custom_fields'] ?? []);
        unset($data['custom_fields']);

        $workItem = app(WorkItemCreator::class)->create($data);

        if (isset($data['labels'])) {
            $labelIds = Label::query()
                ->where('tenant_id', $workItem->tenant_id)
                ->whereIn('id', $data['labels'])
                ->pluck('id')
                ->all();

            $syncData = collect($labelIds)
                ->mapWithKeys(fn ($labelId) => [$labelId => ['tenant_id' => $workItem->tenant_id]])
                ->toArray();

            $workItem->labels()->sync($syncData);
        }

        if ($customFields !== []) {
            [$normalized] = app(CustomFieldManager::class)->validateValues('work_item', $customFields, (int) $workItem->tenant_id, true);
            app(CustomFieldManager::class)->syncForWorkItem($workItem, $normalized);
        }

        return new WorkItemResource($workItem->load(['labels', 'customFieldValues.field']));
    }

    public function show(WorkItem $workItem): WorkItemResource
    {
        $this->authorize('view', $workItem);

        return new WorkItemResource($workItem->load(['labels', 'customFieldValues.field']));
    }

    public function update(UpdateWorkItemRequest $request, WorkItem $workItem): WorkItemResource
    {
        $this->authorize('update', $workItem);

        $data = $request->validated();
        $data['updated_by'] = auth()->id();
        $customFieldsProvided = array_key_exists('custom_fields', $data);
        $customFields = (array) ($data['custom_fields'] ?? []);
        unset($data['custom_fields']);

        if (isset($data['status_id']) && (int) $data['status_id'] !== (int) $workItem->status_id) {
            $targetStatus = $workItem->workflow->statuses()->findOrFail($data['status_id']);
            app(WorkflowTransitionService::class)->transition($workItem, $targetStatus);
            unset($data['status_id']);
        }

        $workItem->update($data);

        if (array_key_exists('labels', $data)) {
            $labels = $data['labels'] ?? [];
            $labelIds = Label::query()
                ->where('tenant_id', $workItem->tenant_id)
                ->whereIn('id', $labels)
                ->pluck('id')
                ->all();

            $syncData = collect($labelIds)
                ->mapWithKeys(fn ($labelId) => [$labelId => ['tenant_id' => $workItem->tenant_id]])
                ->toArray();

            $workItem->labels()->sync($syncData);
        }

        if ($customFieldsProvided) {
            [$normalized] = app(CustomFieldManager::class)->validateValues('work_item', $customFields, (int) $workItem->tenant_id, false);
            app(CustomFieldManager::class)->syncForWorkItem($workItem, $normalized);
        }

        return new WorkItemResource($workItem->refresh()->load(['labels', 'customFieldValues.field']));
    }

    public function destroy(WorkItem $workItem): JsonResponse
    {
        $this->authorize('delete', $workItem);

        $workItem->delete();

        return response()->json([], 204);
    }
}
