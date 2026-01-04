<?php

namespace Haida\FilamentMeetings\Http\Controllers\Api\V1;

use Haida\FilamentMeetings\Models\Meeting;
use Haida\FilamentMeetings\Services\MeetingWorkhubService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeetingActionItemController extends ApiController
{
    public function linkToWorkhub(Request $request, Meeting $meeting, MeetingWorkhubService $service): JsonResponse
    {
        $this->authorize('view', $meeting);

        $data = $request->validate([
            'action_item_ids' => ['required', 'array'],
            'action_item_ids.*' => ['integer'],
            'project_id' => ['nullable', 'integer'],
        ]);

        $result = $service->linkActionItems(
            $meeting,
            array_map('intval', $data['action_item_ids']),
            $request->user(),
            $data['project_id'] ?? null,
        );

        if (! $result['ok']) {
            return response()->json([
                'ok' => false,
                'message' => $result['message'] ?? 'اتصال اقدام‌ها ناموفق بود.',
            ], 422);
        }

        return response()->json([
            'ok' => true,
            'linked' => $result['linked'] ?? 0,
        ]);
    }
}
