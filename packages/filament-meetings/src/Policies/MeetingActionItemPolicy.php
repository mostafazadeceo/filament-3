<?php

namespace Haida\FilamentMeetings\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentMeetings\Models\MeetingActionItem;

class MeetingActionItemPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'meetings.view',
            'meetings.action_items.manage',
        ], null, $user);
    }

    public function view(User $user, MeetingActionItem $item): bool
    {
        return IamAuthorization::allowsAny([
            'meetings.view',
            'meetings.action_items.manage',
        ], IamAuthorization::resolveTenantFromRecord($item), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('meetings.action_items.manage', null, $user);
    }

    public function update(User $user, MeetingActionItem $item): bool
    {
        return IamAuthorization::allows('meetings.action_items.manage', IamAuthorization::resolveTenantFromRecord($item), $user);
    }

    public function delete(User $user, MeetingActionItem $item): bool
    {
        return IamAuthorization::allows('meetings.action_items.manage', IamAuthorization::resolveTenantFromRecord($item), $user);
    }
}
