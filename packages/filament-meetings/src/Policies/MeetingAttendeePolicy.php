<?php

namespace Haida\FilamentMeetings\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentMeetings\Models\MeetingAttendee;

class MeetingAttendeePolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'meetings.view',
            'meetings.manage',
        ], null, $user);
    }

    public function view(User $user, MeetingAttendee $attendee): bool
    {
        return IamAuthorization::allowsAny([
            'meetings.view',
            'meetings.manage',
        ], IamAuthorization::resolveTenantFromRecord($attendee), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('meetings.manage', null, $user);
    }

    public function update(User $user, MeetingAttendee $attendee): bool
    {
        return IamAuthorization::allows('meetings.manage', IamAuthorization::resolveTenantFromRecord($attendee), $user);
    }

    public function delete(User $user, MeetingAttendee $attendee): bool
    {
        return IamAuthorization::allows('meetings.manage', IamAuthorization::resolveTenantFromRecord($attendee), $user);
    }
}
