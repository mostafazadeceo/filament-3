<?php

namespace Haida\FilamentMeetings\Http\Controllers\Api\V1;

use Haida\FilamentMeetings\Models\Meeting;
use Haida\FilamentMeetings\Services\MeetingTranscriptService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeetingTranscriptController extends ApiController
{
    public function manual(Request $request, Meeting $meeting, MeetingTranscriptService $service): JsonResponse
    {
        $this->authorize('view', $meeting);

        $data = $request->validate([
            'content' => ['required', 'string'],
            'language' => ['required', 'string'],
        ]);

        $result = $service->storeTranscript(
            $meeting,
            (string) $data['content'],
            (string) $data['language'],
            'manual',
            $request->user(),
        );

        if (! $result['ok']) {
            return response()->json([
                'ok' => false,
                'message' => $result['message'] ?? 'ثبت متن جلسه ناموفق بود.',
            ], 422);
        }

        return response()->json(['ok' => true]);
    }

    public function upload(Request $request, Meeting $meeting, MeetingTranscriptService $service): JsonResponse
    {
        $this->authorize('view', $meeting);

        $data = $request->validate([
            'file' => ['required', 'file', 'mimes:txt,md'],
            'language' => ['required', 'string'],
        ]);

        $file = $request->file('file');
        $content = $file ? $file->get() : '';

        $result = $service->storeTranscript(
            $meeting,
            (string) $content,
            (string) $data['language'],
            'upload',
            $request->user(),
        );

        if (! $result['ok']) {
            return response()->json([
                'ok' => false,
                'message' => $result['message'] ?? 'ثبت متن جلسه ناموفق بود.',
            ], 422);
        }

        return response()->json(['ok' => true]);
    }
}
