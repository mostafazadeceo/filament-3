<?php

namespace Haida\FilamentMeetings\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentMeetings\Models\MeetingTranscript;

class MeetingTranscriptPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'meetings.transcript.manage',
            'meetings.minutes.manage',
        ], null, $user);
    }

    public function view(User $user, MeetingTranscript $transcript): bool
    {
        return IamAuthorization::allowsAny([
            'meetings.transcript.manage',
            'meetings.minutes.manage',
        ], IamAuthorization::resolveTenantFromRecord($transcript), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('meetings.transcript.manage', null, $user);
    }

    public function update(User $user, MeetingTranscript $transcript): bool
    {
        return IamAuthorization::allows('meetings.transcript.manage', IamAuthorization::resolveTenantFromRecord($transcript), $user);
    }

    public function delete(User $user, MeetingTranscript $transcript): bool
    {
        return IamAuthorization::allows('meetings.transcript.manage', IamAuthorization::resolveTenantFromRecord($transcript), $user);
    }
}
