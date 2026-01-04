<?php

namespace Haida\FilamentMeetings\Http\Controllers\Api\V1;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentMeetings\Http\Resources\MeetingResource;
use Haida\FilamentMeetings\Models\Meeting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class MeetingController extends ApiController
{
    public function index(Request $request): ResourceCollection
    {
        $this->authorize('viewAny', Meeting::class);

        $meetings = Meeting::query()
            ->with(['organizer'])
            ->latest('scheduled_at')
            ->paginate($request->integer('per_page', 50));

        return MeetingResource::collection($meetings);
    }

    public function show(Meeting $meeting): MeetingResource
    {
        $this->authorize('view', $meeting);

        $meeting->load(['organizer', 'attendees', 'agendaItems', 'notes', 'transcripts', 'minutes', 'actionItems']);

        return new MeetingResource($meeting);
    }

    public function store(Request $request): MeetingResource
    {
        $this->authorize('create', Meeting::class);

        $data = $this->validateMeeting($request);
        $data['tenant_id'] = $data['tenant_id'] ?? TenantContext::getTenantId();
        $data['created_by'] = $request->user()?->getAuthIdentifier();
        $data['updated_by'] = $request->user()?->getAuthIdentifier();

        $meeting = Meeting::query()->create($data);

        return new MeetingResource($meeting->load(['organizer']));
    }

    public function update(Request $request, Meeting $meeting): MeetingResource
    {
        $this->authorize('update', $meeting);

        $data = $this->validateMeeting($request, $meeting, false);
        $data['updated_by'] = $request->user()?->getAuthIdentifier();

        $meeting->update($data);

        return new MeetingResource($meeting->refresh()->load(['organizer']));
    }

    public function destroy(Meeting $meeting): JsonResponse
    {
        $this->authorize('delete', $meeting);

        $meeting->delete();

        return response()->json([], 204);
    }

    /**
     * @return array<string, mixed>
     */
    protected function validateMeeting(Request $request, ?Meeting $meeting = null, bool $require = true): array
    {
        if (! $require) {
            return $request->validate([
                'tenant_id' => ['sometimes', 'nullable', 'integer'],
                'title' => ['sometimes', 'string', 'max:255'],
                'scheduled_at' => ['sometimes', 'nullable', 'date'],
                'duration_minutes' => ['sometimes', 'nullable', 'integer', 'min:0'],
                'location_type' => ['sometimes', 'nullable', 'string', 'in:online,onsite'],
                'location_value' => ['sometimes', 'nullable', 'string', 'max:255'],
                'organizer_id' => ['sometimes', 'nullable', 'integer', 'exists:users,id'],
                'status' => ['sometimes', 'nullable', 'string', 'in:draft,scheduled,running,completed,archived'],
                'ai_enabled' => ['sometimes', 'nullable', 'boolean'],
                'consent_required' => ['sometimes', 'nullable', 'boolean'],
                'consent_mode' => ['sometimes', 'nullable', 'string', 'in:manual,per_attendee'],
                'share_minutes_mode' => ['sometimes', 'nullable', 'string', 'in:private,attendees,selected_roles'],
                'minutes_format' => ['sometimes', 'nullable', 'string', 'in:sales,standup,team,custom'],
                'meta' => ['sometimes', 'nullable', 'array'],
            ]);
        }

        return $request->validate([
            'tenant_id' => ['nullable', 'integer'],
            'title' => ['required', 'string', 'max:255'],
            'scheduled_at' => ['nullable', 'date'],
            'duration_minutes' => ['nullable', 'integer', 'min:0'],
            'location_type' => ['nullable', 'string', 'in:online,onsite'],
            'location_value' => ['nullable', 'string', 'max:255'],
            'organizer_id' => ['nullable', 'integer', 'exists:users,id'],
            'status' => ['nullable', 'string', 'in:draft,scheduled,running,completed,archived'],
            'ai_enabled' => ['nullable', 'boolean'],
            'consent_required' => ['nullable', 'boolean'],
            'consent_mode' => ['nullable', 'string', 'in:manual,per_attendee'],
            'share_minutes_mode' => ['nullable', 'string', 'in:private,attendees,selected_roles'],
            'minutes_format' => ['nullable', 'string', 'in:sales,standup,team,custom'],
            'meta' => ['nullable', 'array'],
        ]);
    }
}
