<?php

namespace Haida\FilamentMeetings\Http\Controllers\Api\V1;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentMeetings\Http\Resources\MeetingTemplateResource;
use Haida\FilamentMeetings\Models\MeetingTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class MeetingTemplateController extends ApiController
{
    public function index(Request $request): ResourceCollection
    {
        $this->authorize('viewAny', MeetingTemplate::class);

        $templates = MeetingTemplate::query()
            ->with('owner')
            ->latest('updated_at')
            ->paginate($request->integer('per_page', 50));

        return MeetingTemplateResource::collection($templates);
    }

    public function show(MeetingTemplate $template): MeetingTemplateResource
    {
        $this->authorize('view', $template);

        return new MeetingTemplateResource($template->load('owner'));
    }

    public function store(Request $request): MeetingTemplateResource
    {
        $this->authorize('create', MeetingTemplate::class);

        $data = $this->validateTemplate($request, false);
        $data['tenant_id'] = $data['tenant_id'] ?? TenantContext::getTenantId();
        $data['owner_id'] = ($data['scope'] ?? 'workspace') === 'personal'
            ? $request->user()?->getAuthIdentifier()
            : null;

        $template = MeetingTemplate::query()->create($data);

        return new MeetingTemplateResource($template->load('owner'));
    }

    public function update(Request $request, MeetingTemplate $template): MeetingTemplateResource
    {
        $this->authorize('update', $template);

        $data = $this->validateTemplate($request);
        $data['owner_id'] = ($data['scope'] ?? $template->scope) === 'personal'
            ? ($template->owner_id ?: $request->user()?->getAuthIdentifier())
            : null;

        $template->update($data);

        return new MeetingTemplateResource($template->refresh()->load('owner'));
    }

    public function destroy(MeetingTemplate $template): JsonResponse
    {
        $this->authorize('delete', $template);

        $template->delete();

        return response()->json([], 204);
    }

    /**
     * @return array<string, mixed>
     */
    protected function validateTemplate(Request $request, bool $require = true): array
    {
        if (! $require) {
            return $request->validate([
                'tenant_id' => ['sometimes', 'nullable', 'integer'],
                'name' => ['sometimes', 'string', 'max:255'],
                'format' => ['sometimes', 'nullable', 'string', 'in:sales,standup,team,custom'],
                'scope' => ['sometimes', 'nullable', 'string', 'in:workspace,personal'],
                'sections_enabled_json' => ['sometimes', 'nullable', 'array'],
                'custom_prompts_json' => ['sometimes', 'nullable', 'array'],
                'minutes_schema_json' => ['sometimes', 'nullable', 'array'],
            ]);
        }

        return $request->validate([
            'tenant_id' => ['nullable', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'format' => ['nullable', 'string', 'in:sales,standup,team,custom'],
            'scope' => ['nullable', 'string', 'in:workspace,personal'],
            'sections_enabled_json' => ['nullable', 'array'],
            'custom_prompts_json' => ['nullable', 'array'],
            'minutes_schema_json' => ['nullable', 'array'],
        ]);
    }
}
