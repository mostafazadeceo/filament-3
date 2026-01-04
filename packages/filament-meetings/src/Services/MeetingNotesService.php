<?php

namespace Haida\FilamentMeetings\Services;

use Filamat\IamSuite\Services\AuditService;
use Haida\FilamentMeetings\Models\Meeting;
use Haida\FilamentMeetings\Models\MeetingNote;
use Illuminate\Contracts\Auth\Authenticatable;

class MeetingNotesService
{
    public function __construct(protected AuditService $auditService) {}

    public function saveNotes(Meeting $meeting, string $content, ?Authenticatable $actor = null): MeetingNote
    {
        $note = MeetingNote::query()->updateOrCreate([
            'tenant_id' => $meeting->tenant_id,
            'meeting_id' => $meeting->getKey(),
        ], [
            'content_longtext' => $content,
            'updated_by' => $actor?->getAuthIdentifier(),
        ]);

        $this->auditService->log('meetings.notes.updated', $meeting, [
            'note_id' => $note->getKey(),
        ], $actor);

        return $note;
    }
}
