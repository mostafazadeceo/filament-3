<?php

namespace Haida\FilamentMeetings\Http\Controllers\Api\V1;

use Haida\FilamentMeetings\Http\Resources\MeetingAgendaItemResource;
use Haida\FilamentMeetings\Models\Meeting;
use Haida\FilamentMeetings\Models\MeetingAgendaItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class MeetingAgendaItemController extends ApiController
{
    public function index(Meeting $meeting): ResourceCollection
    {
        $this->authorize('view', $meeting);

        $items = $meeting->agendaItems()->orderBy('sort_order')->paginate(50);

        return MeetingAgendaItemResource::collection($items);
    }

    public function store(Request $request, Meeting $meeting): MeetingAgendaItemResource
    {
        $this->authorize('create', MeetingAgendaItem::class);

        $data = $this->validateAgendaItem($request);
        $data['tenant_id'] = $meeting->tenant_id;
        $data['meeting_id'] = $meeting->getKey();
        $data['sort_order'] = $data['sort_order'] ?? ((int) $meeting->agendaItems()->max('sort_order') + 1);

        $item = MeetingAgendaItem::query()->create($data);

        return new MeetingAgendaItemResource($item);
    }

    public function update(Request $request, MeetingAgendaItem $agendaItem): MeetingAgendaItemResource
    {
        $this->authorize('update', $agendaItem);

        $data = $this->validateAgendaItem($request, false);
        $agendaItem->update($data);

        return new MeetingAgendaItemResource($agendaItem->refresh());
    }

    public function destroy(MeetingAgendaItem $agendaItem): JsonResponse
    {
        $this->authorize('delete', $agendaItem);

        $agendaItem->delete();

        return response()->json([], 204);
    }

    /**
     * @return array<string, mixed>
     */
    protected function validateAgendaItem(Request $request, bool $require = true): array
    {
        return $request->validate([
            'title' => [$require ? 'required' : 'nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'owner_id' => ['nullable', 'integer', 'exists:users,id'],
            'timebox_minutes' => ['nullable', 'integer', 'min:0'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }
}
