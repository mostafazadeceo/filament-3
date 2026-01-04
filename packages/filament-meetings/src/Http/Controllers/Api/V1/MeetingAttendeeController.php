<?php

namespace Haida\FilamentMeetings\Http\Controllers\Api\V1;

use Haida\FilamentMeetings\Http\Resources\MeetingAttendeeResource;
use Haida\FilamentMeetings\Models\Meeting;
use Haida\FilamentMeetings\Models\MeetingAttendee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class MeetingAttendeeController extends ApiController
{
    public function index(Meeting $meeting): ResourceCollection
    {
        $this->authorize('view', $meeting);

        $attendees = $meeting->attendees()->latest('created_at')->paginate(50);

        return MeetingAttendeeResource::collection($attendees);
    }

    public function store(Request $request, Meeting $meeting): MeetingAttendeeResource
    {
        $this->authorize('create', MeetingAttendee::class);

        $data = $this->validateAttendee($request);
        $data['tenant_id'] = $meeting->tenant_id;
        $data['meeting_id'] = $meeting->getKey();
        $data['invited_at'] = $data['invited_at'] ?? now();

        $attendee = MeetingAttendee::query()->create($data);

        return new MeetingAttendeeResource($attendee);
    }

    public function update(Request $request, MeetingAttendee $attendee): MeetingAttendeeResource
    {
        $this->authorize('update', $attendee);

        $data = $this->validateAttendee($request, false);
        $attendee->update($data);

        return new MeetingAttendeeResource($attendee->refresh());
    }

    public function destroy(MeetingAttendee $attendee): JsonResponse
    {
        $this->authorize('delete', $attendee);

        $attendee->delete();

        return response()->json([], 204);
    }

    /**
     * @return array<string, mixed>
     */
    protected function validateAttendee(Request $request, bool $require = true): array
    {
        if (! $require) {
            return $request->validate([
                'user_id' => ['sometimes', 'nullable', 'integer', 'exists:users,id'],
                'name' => ['sometimes', 'nullable', 'string', 'max:255'],
                'email_masked' => ['sometimes', 'nullable', 'string', 'max:255'],
                'role' => ['sometimes', 'nullable', 'string', 'in:host,attendee,guest'],
                'invited_at' => ['sometimes', 'nullable', 'date'],
                'responded_at' => ['sometimes', 'nullable', 'date'],
                'attendance_status' => ['sometimes', 'nullable', 'string', 'in:invited,accepted,declined,attended'],
                'consent_granted_at' => ['sometimes', 'nullable', 'date'],
            ]);
        }

        return $request->validate([
            'user_id' => ['required_without:name', 'nullable', 'integer', 'exists:users,id'],
            'name' => ['required_without:user_id', 'nullable', 'string', 'max:255'],
            'email_masked' => ['nullable', 'string', 'max:255'],
            'role' => ['nullable', 'string', 'in:host,attendee,guest'],
            'invited_at' => ['nullable', 'date'],
            'responded_at' => ['nullable', 'date'],
            'attendance_status' => ['nullable', 'string', 'in:invited,accepted,declined,attended'],
            'consent_granted_at' => ['nullable', 'date'],
        ]);
    }
}
