<?php

namespace Haida\FilamentMeetings\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentMeetings\Models\MeetingAgendaItem;

class MeetingAgendaItemPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'meetings.view',
            'meetings.manage',
        ], null, $user);
    }

    public function view(User $user, MeetingAgendaItem $item): bool
    {
        return IamAuthorization::allowsAny([
            'meetings.view',
            'meetings.manage',
        ], IamAuthorization::resolveTenantFromRecord($item), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('meetings.manage', null, $user);
    }

    public function update(User $user, MeetingAgendaItem $item): bool
    {
        return IamAuthorization::allows('meetings.manage', IamAuthorization::resolveTenantFromRecord($item), $user);
    }

    public function delete(User $user, MeetingAgendaItem $item): bool
    {
        return IamAuthorization::allows('meetings.manage', IamAuthorization::resolveTenantFromRecord($item), $user);
    }
}
