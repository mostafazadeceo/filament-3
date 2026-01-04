<?php

namespace Haida\FilamentMeetings\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentMeetings\Models\Meeting;

class MeetingPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'meetings.view',
            'meetings.manage',
        ], null, $user);
    }

    public function view(User $user, Meeting $meeting): bool
    {
        return IamAuthorization::allowsAny([
            'meetings.view',
            'meetings.manage',
        ], IamAuthorization::resolveTenantFromRecord($meeting), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('meetings.manage', null, $user);
    }

    public function update(User $user, Meeting $meeting): bool
    {
        return IamAuthorization::allows('meetings.manage', IamAuthorization::resolveTenantFromRecord($meeting), $user);
    }

    public function delete(User $user, Meeting $meeting): bool
    {
        return IamAuthorization::allows('meetings.manage', IamAuthorization::resolveTenantFromRecord($meeting), $user);
    }

    public function restore(User $user, Meeting $meeting): bool
    {
        return IamAuthorization::allows('meetings.manage', IamAuthorization::resolveTenantFromRecord($meeting), $user);
    }

    public function forceDelete(User $user, Meeting $meeting): bool
    {
        return IamAuthorization::allows('meetings.manage', IamAuthorization::resolveTenantFromRecord($meeting), $user);
    }
}
