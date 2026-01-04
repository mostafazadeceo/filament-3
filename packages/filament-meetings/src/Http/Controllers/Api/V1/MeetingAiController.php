<?php

namespace Haida\FilamentMeetings\Http\Controllers\Api\V1;

use Haida\FilamentMeetings\Models\Meeting;
use Haida\FilamentMeetings\Services\MeetingsAiService;
use Illuminate\Http\JsonResponse;

class MeetingAiController extends ApiController
{
    public function generateAgenda(Meeting $meeting, MeetingsAiService $service): JsonResponse
    {
        $this->authorize('view', $meeting);

        if ($service->shouldQueue()) {
            $message = $service->canGenerateAgenda($meeting);
            if ($message) {
                return response()->json([
                    'ok' => false,
                    'message' => $message,
                ], 422);
            }

            $service->queueAgenda($meeting, request()->user());

            return response()->json([
                'ok' => true,
                'queued' => true,
            ], 202);
        }

        $result = $service->generateAgenda($meeting, request()->user());

        if (! $result['ok']) {
            return response()->json([
                'ok' => false,
                'message' => $result['message'] ?? 'درخواست ناموفق بود.',
            ], 422);
        }

        return response()->json([
            'ok' => true,
            'agenda' => $result['agenda'] ?? [],
            'provider' => $result['result']?->provider,
            'model' => $result['result']?->model,
        ]);
    }

    public function generateMinutes(Meeting $meeting, MeetingsAiService $service): JsonResponse
    {
        $this->authorize('view', $meeting);

        if ($service->shouldQueue()) {
            $message = $service->canGenerateMinutes($meeting, request()->user());
            if ($message) {
                return response()->json([
                    'ok' => false,
                    'message' => $message,
                ], 422);
            }

            $service->queueMinutes($meeting, request()->user());

            return response()->json([
                'ok' => true,
                'queued' => true,
            ], 202);
        }

        $result = $service->generateMinutes($meeting, request()->user());

        if (! $result['ok']) {
            $minutes = $result['minutes'] ?? null;

            return response()->json([
                'ok' => false,
                'message' => $result['message'] ?? 'درخواست ناموفق بود.',
                'minutes_id' => $minutes?->getKey(),
            ], 422);
        }

        return response()->json([
            'ok' => true,
            'minutes_id' => $result['minutes']?->getKey(),
            'provider' => $result['result']?->provider,
            'model' => $result['result']?->model,
        ]);
    }

    public function recap(Meeting $meeting, MeetingsAiService $service): JsonResponse
    {
        $this->authorize('view', $meeting);

        if ($service->shouldQueue()) {
            $message = $service->canGenerateRecap($meeting);
            if ($message) {
                return response()->json([
                    'ok' => false,
                    'message' => $message,
                ], 422);
            }

            $service->queueRecap($meeting, request()->user());

            return response()->json([
                'ok' => true,
                'queued' => true,
            ], 202);
        }

        $result = $service->generateRecap($meeting, request()->user());

        if (! $result['ok']) {
            return response()->json([
                'ok' => false,
                'message' => $result['message'] ?? 'درخواست ناموفق بود.',
            ], 422);
        }

        return response()->json([
            'ok' => true,
            'summary' => $result['summary'] ?? '',
        ]);
    }
}
