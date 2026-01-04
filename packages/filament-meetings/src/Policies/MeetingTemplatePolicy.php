<?php

namespace Haida\FilamentMeetings\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentMeetings\Models\MeetingTemplate;

class MeetingTemplatePolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'meetings.view',
            'meetings.templates.manage',
        ], null, $user);
    }

    public function view(User $user, MeetingTemplate $template): bool
    {
        return IamAuthorization::allowsAny([
            'meetings.view',
            'meetings.templates.manage',
        ], IamAuthorization::resolveTenantFromRecord($template), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('meetings.templates.manage', null, $user);
    }

    public function update(User $user, MeetingTemplate $template): bool
    {
        return IamAuthorization::allows('meetings.templates.manage', IamAuthorization::resolveTenantFromRecord($template), $user);
    }

    public function delete(User $user, MeetingTemplate $template): bool
    {
        return IamAuthorization::allows('meetings.templates.manage', IamAuthorization::resolveTenantFromRecord($template), $user);
    }
}
