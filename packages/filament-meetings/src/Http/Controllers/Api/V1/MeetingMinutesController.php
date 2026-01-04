<?php

namespace Haida\FilamentMeetings\Http\Controllers\Api\V1;

use Haida\FilamentMeetings\Models\Meeting;
use Haida\FilamentMeetings\Services\MeetingMinutesExportService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MeetingMinutesController extends ApiController
{
    public function export(Meeting $meeting, MeetingMinutesExportService $service): StreamedResponse
    {
        $this->authorize('view', $meeting);

        return $service->exportMarkdown($meeting);
    }
}
