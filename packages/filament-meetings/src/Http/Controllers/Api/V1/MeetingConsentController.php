<?php

namespace Haida\FilamentMeetings\Http\Controllers\Api\V1;

use Haida\FilamentMeetings\Models\Meeting;
use Haida\FilamentMeetings\Services\MeetingConsentService;
use Illuminate\Http\JsonResponse;

class MeetingConsentController extends ApiController
{
    public function confirm(Meeting $meeting, MeetingConsentService $service): JsonResponse
    {
        $this->authorize('view', $meeting);

        $result = $service->confirmConsent($meeting, request()->user());

        if (! $result['ok']) {
            return response()->json([
                'ok' => false,
                'message' => $result['message'] ?? 'ثبت رضایت ناموفق بود.',
            ], 422);
        }

        return response()->json(['ok' => true]);
    }
}
