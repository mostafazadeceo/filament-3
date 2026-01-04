<?php

namespace Haida\FilamentMeetings\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentMeetings\Models\MeetingNote;

class MeetingNotePolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'meetings.view',
            'meetings.manage',
        ], null, $user);
    }

    public function view(User $user, MeetingNote $note): bool
    {
        return IamAuthorization::allowsAny([
            'meetings.view',
            'meetings.manage',
        ], IamAuthorization::resolveTenantFromRecord($note), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('meetings.manage', null, $user);
    }

    public function update(User $user, MeetingNote $note): bool
    {
        return IamAuthorization::allows('meetings.manage', IamAuthorization::resolveTenantFromRecord($note), $user);
    }

    public function delete(User $user, MeetingNote $note): bool
    {
        return IamAuthorization::allows('meetings.manage', IamAuthorization::resolveTenantFromRecord($note), $user);
    }
}
