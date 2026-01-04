<?php

namespace Haida\FilamentMeetings\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentMeetings\Models\MeetingMinute;

class MeetingMinutePolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'meetings.view',
            'meetings.minutes.manage',
        ], null, $user);
    }

    public function view(User $user, MeetingMinute $minute): bool
    {
        return IamAuthorization::allowsAny([
            'meetings.view',
            'meetings.minutes.manage',
        ], IamAuthorization::resolveTenantFromRecord($minute), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('meetings.minutes.manage', null, $user);
    }

    public function update(User $user, MeetingMinute $minute): bool
    {
        return IamAuthorization::allows('meetings.minutes.manage', IamAuthorization::resolveTenantFromRecord($minute), $user);
    }

    public function delete(User $user, MeetingMinute $minute): bool
    {
        return IamAuthorization::allows('meetings.minutes.manage', IamAuthorization::resolveTenantFromRecord($minute), $user);
    }
}
