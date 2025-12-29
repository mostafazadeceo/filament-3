<?php

namespace Haida\FilamentWorkhub\Http\Controllers\Api\V1;

use Haida\FilamentWorkhub\Http\Requests\AutomationRuleRequest;
use Haida\FilamentWorkhub\Http\Resources\AutomationRuleResource;
use Haida\FilamentWorkhub\Models\AutomationRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

class AutomationRuleController extends ApiController
{
    public function index(): ResourceCollection
    {
        $this->authorize('viewAny', AutomationRule::class);

        return AutomationRuleResource::collection(
            AutomationRule::query()->latest()->paginate(50)
        );
    }

    public function store(AutomationRuleRequest $request): AutomationRuleResource
    {
        $this->authorize('create', AutomationRule::class);

        $rule = AutomationRule::query()->create($request->validated());

        return new AutomationRuleResource($rule);
    }

    public function show(AutomationRule $automationRule): AutomationRuleResource
    {
        $this->authorize('view', $automationRule);

        return new AutomationRuleResource($automationRule);
    }

    public function update(AutomationRuleRequest $request, AutomationRule $automationRule): AutomationRuleResource
    {
        $this->authorize('update', $automationRule);

        $automationRule->update($request->validated());

        return new AutomationRuleResource($automationRule->refresh());
    }

    public function destroy(AutomationRule $automationRule): JsonResponse
    {
        $this->authorize('delete', $automationRule);

        $automationRule->delete();

        return response()->json([], 204);
    }
}
